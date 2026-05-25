<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ContactImageUploadRequest;
use App\Http\Requests\Api\V1\ContactsIndexRequest;
use App\Http\Requests\Api\V1\ContactsStoreRequest;
use App\Http\Requests\Api\V1\ContactsUpdateRequest;
use App\Http\Requests\Api\V1\LookupContactByNumberRequest;
use App\Models\Contact;
use App\Models\ContactNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Intervention\Image\Facades\Image;

class ContactsController extends Controller
{
    protected ?string $accessEntity = 'contacts';

    public function index(ContactsIndexRequest $request): JsonResponse
    {
        $this->can('view');

        $query = trim((string) $request->query('q', ''));
        // FormRequest enforced 1..100 already; just supply the default.
        $perPage = (int) $request->query('per_page', 25);

        // Mirrors the web index: Meilisearch when a query is present, plain
        // sorted+active scope otherwise. Tenant scoping comes from the
        // BelongsToTenantScope global scope on Contact (driven by SetTenant
        // middleware on this route).
        $contacts = $query !== ''
            ? Contact::search($query)->paginate($perPage)
            : Contact::sorted()->active()->paginate($perPage);

        // Eager-load numbers on the paginated slice (not the underlying
        // query) so both the Meilisearch and Eloquent paths get the same
        // single follow-up SELECT instead of one-per-contact. The Android
        // dialer reads these into Room on sync; shipping them in the list
        // avoids N+1 detail calls just to populate the local number index.
        $contacts->getCollection()->load('numbers');

        return response()->json([
            'data' => $contacts->getCollection()->map(fn (Contact $c) => $this->serialize($c))->values(),
            'meta' => [
                'current_page' => $contacts->currentPage(),
                'last_page' => $contacts->lastPage(),
                'per_page' => $contacts->perPage(),
                'total' => $contacts->total(),
                'from' => $contacts->firstItem(),
                'to' => $contacts->lastItem(),
            ],
            'q' => $query,
        ]);
    }

    /**
     * Reverse phone-number lookup for incoming-call display on the Android
     * client. Designed to be cheap so the screening service can use it
     * inside its ~5s budget — minimal payload, no relation loads beyond
     * the parent contact, tenant-scoped via the BelongsToTenantScope on
     * Contact (joined through contact_numbers).
     *
     * Matching strategy:
     *   1) Parse the input to E.164 via libphonenumber. Exact-match against
     *      the indexed `e164` column — fast, no false positives.
     *   2) If parsing fails (partial extension, very short number, junk),
     *      fall back to a digit-suffix match (last 7+ digits) against
     *      `number`. Required ≥7 digits cuts false positives down to
     *      "nearly impossible for personal contact volumes".
     *
     * Returns an array even though there's usually 0 or 1 match — multiple
     * is legitimate (a shared household line, a number stored under two
     * people) and we want the client to be able to surface all of them.
     */
    public function byNumber(LookupContactByNumberRequest $request): JsonResponse
    {
        $this->can('view');

        $input = $request->query('number');
        $util = \libphonenumber\PhoneNumberUtil::getInstance();
        $region = config('contacts.phone_default_region', 'AT');

        $matches = collect();

        // Path 1: parse → exact E.164 match.
        try {
            $parsed = $util->parse($input, $region);
            if ($util->isValidNumber($parsed)) {
                $e164 = $util->format($parsed, \libphonenumber\PhoneNumberFormat::E164);
                $matches = ContactNumber::query()
                    ->with('contact:id,ulid,firstname,lastname,company,nickname,title,title_after')
                    ->whereHas('contact')
                    ->where('e164', $e164)
                    ->get();
            }
        } catch (\libphonenumber\NumberParseException) {
            // fall through
        }

        // Path 2: digit-suffix fallback for unparseable / partial inputs.
        if ($matches->isEmpty()) {
            $normalised = preg_replace('/\D/', '', (string) $input);
            $suffix = substr($normalised, -8);

            if (strlen($suffix) >= 7) {
                $matches = ContactNumber::query()
                    ->with('contact:id,ulid,firstname,lastname,company,nickname,title,title_after')
                    ->whereHas('contact')
                    ->get()
                    ->filter(function (ContactNumber $n) use ($normalised, $suffix) {
                        $stored = preg_replace('/\D/', '', (string) $n->number);
                        if ($stored === '') return false;

                        return $stored === $normalised
                            || str_ends_with($stored, $suffix);
                    });
            }
        }

        return response()->json([
            'data' => $matches->map(fn (ContactNumber $n) => [
                'contact_ulid' => $n->contact->ulid,
                'fullname' => $n->contact->fullname,
                'image_url' => $n->contact->image ? url('storage/'.$n->contact->image) : null,
                'matched_number' => [
                    'name' => $n->name,
                    'number' => $n->number,
                    'e164' => $n->e164,
                ],
            ])->values(),
        ]);
    }

    public function store(ContactsStoreRequest $request): JsonResponse
    {
        $contact = new Contact();
        $contact->fill($request->validated());
        $contact->save();

        if (is_array($request->input('contact_groups'))) {
            $contact->contactGroups()->sync($request->input('contact_groups'));
        }

        return response()->json([
            'data' => $this->serializeDetail($this->withRelations($contact)),
        ], 201);
    }

    /**
     * PATCH-style update — only the fields present in the body are touched.
     * Wired to both PUT and PATCH verbs because clients vary on which they
     * use for "partial update".
     */
    public function update(ContactsUpdateRequest $request, Contact $contact): JsonResponse
    {
        $contact->fill($request->validated());
        $contact->save();

        // Only re-sync groups if the client included the key. Sending an
        // empty array IS meaningful (removes all groups); leaving the key
        // out preserves the current set.
        if ($request->has('contact_groups')) {
            $contact->contactGroups()->sync($request->input('contact_groups') ?? []);
        }

        return response()->json([
            'data' => $this->serializeDetail($this->withRelations($contact)),
        ]);
    }

    /**
     * Upload (or replace) a contact's avatar. Accepts a multipart `file`
     * field (jpeg/png, max 8MB) — the Android client uploads directly,
     * having optionally cropped client-side. The server downsizes to
     * 400×400 regardless of input dimensions as a safety net so storage
     * usage stays predictable. Any prior image file is deleted to avoid
     * orphans.
     */
    public function uploadImage(ContactImageUploadRequest $request, Contact $contact): JsonResponse
    {
        // Visibility:public → 0755 dirs so nginx can serve the file through
        // the public/storage symlink. The default disk would store it
        // 0700-private and the symlinked URL would 403.
        $fileName = $request->file('file')->storePublicly('contact_images', 'public');

        Image::make(storage_path('app/public/').$fileName)
            ->fit(400, 400)
            ->save();

        if ($contact->image && file_exists(storage_path('app/public/').$contact->image)) {
            unlink(storage_path('app/public/').$contact->image);
        }

        $contact->image = $fileName;
        $contact->save();

        return response()->json([
            'data' => $this->serializeDetail($this->withRelations($contact)),
        ]);
    }

    /**
     * Remove a contact's avatar — clears the `image` column and deletes
     * the underlying file. Idempotent: 204 even if there was no image.
     */
    public function destroyImage(Contact $contact): Response
    {
        $this->can('edit');

        if ($contact->image) {
            if (file_exists(storage_path('app/public/').$contact->image)) {
                unlink(storage_path('app/public/').$contact->image);
            }

            $contact->image = null;
            $contact->save();
        }

        return response()->noContent();
    }

    public function destroy(Contact $contact): Response
    {
        $this->can('delete');

        // Mirrors the web controller's pre-delete avatar cleanup so we
        // don't leak orphaned storage files. Cascades on contact_id (FK
        // ON DELETE CASCADE) take care of numbers/emails/addresses/etc.
        if ($contact->image && file_exists(storage_path('app/public/').$contact->image)) {
            unlink(storage_path('app/public/').$contact->image);
        }

        $contact->delete();

        return response()->noContent();
    }

    public function show(Contact $contact): JsonResponse
    {
        $this->can('view');

        // Eager-load every relation the detail view exposes so this returns
        // everything in one round-trip (no N+1, no per-section follow-up
        // requests like the web's lazy-loaded slide-overs).
        return response()->json([
            'data' => $this->serializeDetail($this->withRelations($contact)),
        ]);
    }

    /**
     * Shared eager-load set used by show/store/update so the response shape
     * is identical across read and write. Returns the same instance for
     * method chaining.
     */
    private function withRelations(Contact $contact): Contact
    {
        return $contact->load([
            'gender:id,gender',
            'country:id,country',
            'contactGroups:id,name',
            'numbers',
            'emails',
            'urls',
            'notes',
            'dates',
            'calls',
            'giftIdeas',
            'addresses.country:id,country',
        ]);
    }

    private function serialize(Contact $contact): array
    {
        return [
            'ulid' => $contact->ulid,
            'fullname' => $contact->fullname,
            'firstname' => $contact->firstname,
            'lastname' => $contact->lastname,
            'company' => $contact->company,
            'image_url' => $contact->image ? url('storage/'.$contact->image) : null,
            // Same shape the detail endpoint uses — Android's Room sync
            // reads these into the dialer's local lookup table on every
            // list response, so it can match incoming calls without
            // hitting /by-number on the cellular hot path.
            'numbers' => $contact->numbers->map(fn ($n) => [
                'ulid' => $n->ulid,
                'name' => $n->name,
                'number' => $n->number,
                'e164' => $n->e164,
            ])->values(),
        ];
    }

    private function serializeDetail(Contact $contact): array
    {
        return [
            // Identifying / display
            'ulid' => $contact->ulid,
            'fullname' => $contact->fullname,
            'firstname' => $contact->firstname,
            'lastname' => $contact->lastname,
            'nickname' => $contact->nickname,
            'title' => $contact->title,
            'title_after' => $contact->title_after,
            'salutation' => $contact->salutation,
            'image_url' => $contact->image ? url('storage/'.$contact->image) : null,
            'active' => (bool) $contact->active,

            // Professional
            'company' => $contact->company,
            'job' => $contact->job,
            'department' => $contact->department,
            'custom_id' => $contact->custom_id,
            'iban' => $contact->iban,
            'vatin' => $contact->vatin,

            // Personal
            'date_of_birth' => $contact->date_of_birth,
            'died_at' => $contact->died_at,
            'died_from' => $contact->died_from,
            'first_met' => $contact->first_met,
            'note' => $contact->note,

            // Relations — flat objects/arrays, never IDs the client has to resolve
            'gender' => $contact->gender ? ['id' => $contact->gender->id, 'name' => $contact->gender->gender] : null,
            'nationality' => $contact->country ? ['id' => $contact->country->id, 'name' => $contact->country->country] : null,
            'contact_groups' => $contact->contactGroups->map(fn ($g) => [
                'id' => $g->id,
                'name' => $g->name,
            ])->values(),

            'numbers' => $contact->numbers->map(fn ($n) => [
                'ulid' => $n->ulid,
                'name' => $n->name,
                'number' => $n->number,
                'e164' => $n->e164,
            ])->values(),

            'emails' => $contact->emails->map(fn ($e) => [
                'ulid' => $e->ulid,
                'name' => $e->name,
                'email' => $e->email,
            ])->values(),

            'urls' => $contact->urls->map(fn ($u) => [
                'ulid' => $u->ulid,
                'name' => $u->name,
                'url' => $u->url,
            ])->values(),

            'notes' => $contact->notes->map(fn ($n) => [
                'ulid' => $n->ulid,
                'name' => $n->name,
                'note' => $n->note,
            ])->values(),

            'addresses' => $contact->addresses->map(fn ($a) => [
                'ulid' => $a->ulid,
                'name' => $a->name,
                'street' => $a->street,
                'zip' => $a->zip,
                'city' => $a->city,
                'state' => $a->state,
                'country' => $a->country?->country,
                'latitude' => $a->latitude !== null ? (float) $a->latitude : null,
                'longitude' => $a->longitude !== null ? (float) $a->longitude : null,
            ])->values(),

            'dates' => $contact->dates->map(fn ($d) => [
                'ulid' => $d->ulid,
                'name' => $d->name,
                'date' => $d->date ? substr($d->date, 0, 10) : null,
                'skip_year' => (bool) $d->skip_year,
            ])->values(),

            'calls' => $contact->calls->map(fn ($c) => [
                'ulid' => $c->ulid,
                'called_at' => $c->called_at ? substr($c->called_at, 0, 16) : null,
                'note' => $c->note,
            ])->values(),

            'gift_ideas' => $contact->giftIdeas->map(fn ($g) => [
                'ulid' => $g->ulid,
                'name' => $g->name,
                'description' => $g->description,
                'url' => $g->url,
                'due_at' => $g->due_at,
            ])->values(),
        ];
    }
}
