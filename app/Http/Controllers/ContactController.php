<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Country;
use App\Models\Gender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\Contact\ContactStoreRequest;
use App\Http\Requests\Contact\ContactUpdateRequest;

class ContactController extends Controller
{
    protected ?string $accessEntity = 'contacts';

    public function index(Request $request): Response
    {
        $this->can('view');

        $query = trim((string) $request->query('q', ''));

        if ($query !== '') {
            $contacts = Contact::search($query)->paginate(10);
        } else {
            $contacts = Contact::sorted()->active()->paginate(10);
        }

        return Inertia::render('Contacts/Index', [
            'contacts' => $contacts->withQueryString()->through(fn ($contact) => [
                'id' => $contact->id,
                'ulid' => $contact->ulid,
                'fullname' => $contact->fullname,
            ]),
            'q' => $query,
            'canCreate' => Auth::user()->checkPermissionTo('create contacts'),
        ]);
    }

    public function create(): Response
    {
        $this->can('create');

        return Inertia::render('Contacts/Create', [
            'genders' => Gender::all(['id', 'gender']),
            'contactGroups' => ContactGroup::sorted()->get(['id', 'name']),
            'countries' => Country::all(['id', 'country']),
        ]);
    }

    public function store(ContactStoreRequest $request): RedirectResponse
    {
        $contact = new Contact();
        $contact->fill($request->all());

        if ($contact->save()) {
            if ( ! is_null($request->contact_groups) && is_array($request->contact_groups)) {
                $contact->contactGroups()->sync($request->contact_groups);
            } else {
                $contact->contactGroups()->sync([]);
            }

            Session::flash('alert-success', trans('flash_message.contact.created'));

            return redirect()->route('contacts.show', [$contact->ulid]);
        }

        Session::flash('alert-danger', trans('flash_message.contact.not_created'));

        return redirect()->route('contacts.create');
    }

    public function show(Contact $contact): Response
    {
        $this->can('view');

        $contact->loadCount([
            'addresses',
            'numbers',
            'emails',
            'urls',
            'dates',
            'notes',
            'calls',
            'giftIdeas as gift_ideas_count',
        ]);
        $contact->load(['gender:id,gender', 'country:id,country']);

        $user = Auth::user();

        return Inertia::render('Contacts/Show', [
            'contact' => [
                'id' => $contact->id,
                'ulid' => $contact->ulid,
                'fullname' => $contact->fullname,
                'salutation' => $contact->salutation,
                'company' => $contact->company,
                'job' => $contact->job,
                'department' => $contact->department,
                'nickname' => $contact->nickname,
                'custom_id' => $contact->custom_id,
                'iban' => $contact->iban,
                'note' => $contact->note,
                'first_met' => $contact->first_met,
                'image' => $contact->image,
                'formatted_date_of_birth' => $contact->formatted_date_of_birth,
                'gender' => $contact->gender ? ['gender' => $contact->gender->gender] : null,
                'nationality' => $contact->country ? ['country' => $contact->country->country] : null,
                'addresses_count' => $contact->addresses_count,
                'numbers_count' => $contact->numbers_count,
                'emails_count' => $contact->emails_count,
                'urls_count' => $contact->urls_count,
                'dates_count' => $contact->dates_count,
                'notes_count' => $contact->notes_count,
                'calls_count' => $contact->calls_count,
                'gift_ideas_count' => $contact->gift_ideas_count,
            ],
            // Lazy props — only loaded when a Section slideover requests them
            // via router.reload({ only: ['numbers'] }) etc.
            'numbers' => Inertia::optional(fn () => $contact->numbers->map(fn ($n) => [
                'id' => $n->id,
                'ulid' => $n->ulid,
                'name' => $n->name,
                'number' => $n->number,
            ])->values()),
            'emails' => Inertia::optional(fn () => $contact->emails->map(fn ($e) => [
                'id' => $e->id,
                'ulid' => $e->ulid,
                'name' => $e->name,
                'email' => $e->email,
            ])->values()),
            'urls' => Inertia::optional(fn () => $contact->urls->map(fn ($u) => [
                'id' => $u->id,
                'ulid' => $u->ulid,
                'name' => $u->name,
                'url' => $u->url,
            ])->values()),
            'notes' => Inertia::optional(fn () => $contact->notes->map(fn ($n) => [
                'id' => $n->id,
                'ulid' => $n->ulid,
                'name' => $n->name,
                'note' => $n->note,
            ])->values()),
            'calls' => Inertia::optional(fn () => $contact->calls->map(fn ($c) => [
                'ulid' => $c->ulid,
                'formatted_called_at' => $c->formatted_called_at,
                'note' => $c->note,
            ])->values()),
            'dates' => Inertia::optional(fn () => $contact->dates->map(fn ($d) => [
                'id' => $d->id,
                'ulid' => $d->ulid,
                'name' => $d->name,
                'formatted_date' => $d->formatted_date,
                'skip_year' => (bool) $d->skip_year,
            ])->values()),
            'gift_ideas' => Inertia::optional(fn () => $contact->giftIdeas->map(fn ($g) => [
                'ulid' => $g->ulid,
                'name' => $g->name,
                'description' => $g->description,
                'url' => $g->url,
                'formatted_due_at' => $g->formatted_due_at,
            ])->values()),
            'addresses' => Inertia::optional(fn () => $contact->addresses->load('country:id,country')->map(fn ($a) => [
                'id' => $a->id,
                'ulid' => $a->ulid,
                'name' => $a->name,
                'street' => $a->street,
                'zip' => $a->zip,
                'city' => $a->city,
                'state' => $a->state,
                'country_id' => $a->country_id,
                'country' => $a->country?->country,
                'latitude' => $a->latitude,
                'longitude' => $a->longitude,
            ])->values()),
            'countries' => Inertia::optional(fn () => Country::all(['id', 'country'])),
            'can' => [
                'edit' => $user->checkPermissionTo('edit contacts'),
                'delete' => $user->checkPermissionTo('delete contacts'),
                'view_addresses' => $user->checkPermissionTo('view addresses'),
                'create_addresses' => $user->checkPermissionTo('create addresses'),
                'edit_addresses' => $user->checkPermissionTo('edit addresses'),
                'delete_addresses' => $user->checkPermissionTo('delete addresses'),
                'view_numbers' => $user->checkPermissionTo('view numbers'),
                'create_numbers' => $user->checkPermissionTo('create numbers'),
                'edit_numbers' => $user->checkPermissionTo('edit numbers'),
                'delete_numbers' => $user->checkPermissionTo('delete numbers'),
                'view_emails' => $user->checkPermissionTo('view emails'),
                'create_emails' => $user->checkPermissionTo('create emails'),
                'edit_emails' => $user->checkPermissionTo('edit emails'),
                'delete_emails' => $user->checkPermissionTo('delete emails'),
                'view_urls' => $user->checkPermissionTo('view urls'),
                'create_urls' => $user->checkPermissionTo('create urls'),
                'edit_urls' => $user->checkPermissionTo('edit urls'),
                'delete_urls' => $user->checkPermissionTo('delete urls'),
                'view_dates' => $user->checkPermissionTo('view dates'),
                'create_dates' => $user->checkPermissionTo('create dates'),
                'edit_dates' => $user->checkPermissionTo('edit dates'),
                'delete_dates' => $user->checkPermissionTo('delete dates'),
                'view_notes' => $user->checkPermissionTo('view notes'),
                'create_notes' => $user->checkPermissionTo('create notes'),
                'edit_notes' => $user->checkPermissionTo('edit notes'),
                'delete_notes' => $user->checkPermissionTo('delete notes'),
                'view_calls' => $user->checkPermissionTo('view calls'),
                'create_calls' => $user->checkPermissionTo('create calls'),
                'edit_calls' => $user->checkPermissionTo('edit calls'),
                'delete_calls' => $user->checkPermissionTo('delete calls'),
                'view_gift_ideas' => $user->checkPermissionTo('view giftIdeas'),
                'create_gift_ideas' => $user->checkPermissionTo('create giftIdeas'),
                'edit_gift_ideas' => $user->checkPermissionTo('edit giftIdeas'),
                'delete_gift_ideas' => $user->checkPermissionTo('delete giftIdeas'),
            ],
        ]);
    }

    public function edit(Contact $contact): Response
    {
        $this->can('edit');

        return Inertia::render('Contacts/Edit', [
            'contact' => [
                'id' => $contact->id,
                'ulid' => $contact->ulid,
                'fullname' => $contact->fullname,
                'salutation' => $contact->salutation,
                'title' => $contact->title,
                'title_after' => $contact->title_after,
                'firstname' => $contact->firstname,
                'lastname' => $contact->lastname,
                'nickname' => $contact->nickname,
                'formatted_date_of_birth' => $contact->formatted_date_of_birth,
                'iban' => $contact->iban,
                'company' => $contact->company,
                'vatin' => $contact->vatin,
                'department' => $contact->department,
                'job' => $contact->job,
                'gender_id' => $contact->gender_id,
                'custom_id' => $contact->custom_id,
                'contact_groups' => $contact->contactGroups->pluck('id')->all(),
                'active' => $contact->active,
                'first_met' => $contact->first_met,
                'note' => $contact->note,
                'formatted_died_at' => $contact->formatted_died_at,
                'died_from' => $contact->died_from,
                'nationality_id' => $contact->nationality_id,
            ],
            'genders' => Gender::all(['id', 'gender']),
            'contactGroups' => ContactGroup::sorted()->get(['id', 'name']),
            'countries' => Country::all(['id', 'country']),
        ]);
    }

    public function update(ContactUpdateRequest $request, Contact $contact): RedirectResponse
    {
        if ( ! is_null($request->contact_groups) && is_array($request->contact_groups)) {
            $contact->contactGroups()->sync($request->contact_groups);
        } else {
            $contact->contactGroups()->sync([]);
        }

        if ($contact->update($request->all())) {
            flashSuccess(trans('flash_message.contact.updated'));

            return redirect()->route('contacts.show', [$contact->ulid]);
        }

        flashError(trans('flash_message.contact.not_updated'));

        return redirect()->route('contacts.edit', [$contact->ulid]);
    }

    public function destroy(Contact $contact): RedirectResponse
    {
        $this->can('delete');

        if ($contact->image) {
            if (file_exists(storage_path('app/public/') . $contact->image)) {
                unlink(storage_path('app/public/') . $contact->image);
            }
        }

        if ($contact->delete()) {
            Session::flash('alert-success', trans('flash_message.contact.deleted'));

            return redirect()->route('contacts.index');
        }

        Session::flash('alert-danger', trans('flash_message.contact.not_deleted'));

        return redirect()->route('contacts.delete', [$contact->ulid]);
    }

    public function delete(Contact $contact): Response
    {
        $this->can('delete');

        return Inertia::render('Contacts/Delete', [
            'contact' => [
                'ulid' => $contact->ulid,
                'fullname' => $contact->fullname,
            ],
        ]);
    }

    public function image(Contact $contact): Response
    {
        $this->can('edit');

        return Inertia::render('Contacts/Image', [
            'contact' => [
                'ulid' => $contact->ulid,
                'fullname' => $contact->fullname,
                'image' => $contact->image,
            ],
        ]);
    }

    public function updateImage(Request $request, Contact $contact): RedirectResponse
    {
        $this->can('edit');

        $this->validate($request, [
            'file' => 'required|mimes:jpeg,png',
        ]);

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            // Route through the 'public' disk (visibility: public → 0755 dirs
            // so nginx can serve the file via the public/storage symlink).
            $fileNameOriginal = $request->file('file')->storePublicly('contact_images', 'public');

            // Cap server-side to 400x400 even if the client uploaded larger.
            // The Vue cropper already produces 400x400 PNG; this is a safety net.
            Image::make(storage_path('app/public/') . $fileNameOriginal)
                ->fit(400, 400)
                ->save();

            if ($contact->image) {
                if (file_exists(storage_path('app/public/') . $contact->image)) {
                    unlink(storage_path('app/public/') . $contact->image);
                }
            }

            $contact->image = str_replace('public/', '', $fileNameOriginal);

            if ($contact->save()) {
                Session::flash('alert-success', trans('flash_message.contact.updated'));

                return redirect()->route('contacts.show', $contact->ulid);
            }
        }

        Session::flash('alert-danger', trans('flash_message.contact.not_updated'));

        return redirect()->route('contacts.image', $contact->ulid);
    }
}
