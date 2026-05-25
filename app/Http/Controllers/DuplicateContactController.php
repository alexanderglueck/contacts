<?php

namespace App\Http\Controllers;

use App\Http\Requests\MergeContactRequest;
use App\Models\Contact;
use App\Services\DuplicateContactDetector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DuplicateContactController extends Controller
{
    public function index(): Response
    {
        $detector = new DuplicateContactDetector(auth()->user()->current_team_id);

        return Inertia::render('Contacts/Duplicates/Index', [
            'groups' => $detector->find(),
        ]);
    }

    public function compare(string $leftUlid, string $rightUlid): Response|RedirectResponse
    {
        if ($leftUlid === $rightUlid) {
            return redirect()->route('duplicates.index')
                ->with('alert-danger', __('duplicates.errors.same_contact'));
        }

        $left = Contact::where('ulid', $leftUlid)->with($this->relationsForCompare())->firstOrFail();
        $right = Contact::where('ulid', $rightUlid)->with($this->relationsForCompare())->firstOrFail();

        return Inertia::render('Contacts/Duplicates/Compare', [
            'left' => $this->shapeForCompare($left),
            'right' => $this->shapeForCompare($right),
            'fields' => MergeContactRequest::FIELDS,
        ]);
    }

    public function merge(MergeContactRequest $request): RedirectResponse
    {
        $kept = Contact::where('ulid', $request->input('kept_ulid'))->firstOrFail();
        $loser = Contact::where('ulid', $request->input('loser_ulid'))->firstOrFail();

        DB::transaction(function () use ($kept, $loser, $request) {
            // Apply scalar choices. 'left' = kept, 'right' = loser, in the
            // frame of the Compare page. Validation already guaranteed every
            // field with content on either side has a choice.
            foreach (MergeContactRequest::FIELDS as $field) {
                $choice = $request->input("choices.{$field}");

                if ($choice === 'right') {
                    $kept->{$field} = $loser->{$field};
                }
                // 'left' or null → keep what's already on $kept; no-op.
            }

            // Image needs filesystem awareness: the chosen file stays, the
            // other one is unlinked so we don't leave orphans. The kept
            // contact's previous image (if it's being replaced) also goes.
            $this->reconcileImage($kept, $loser, $request->input('choices.image'));

            $kept->save();

            // Re-parent every hasMany sub-resource. Listed explicitly rather
            // than reflected so a new relation doesn't silently miss merging.
            $hasMany = [
                'contact_urls', 'contact_numbers', 'contact_emails',
                'contact_dates', 'contact_addresses', 'comments',
                'gift_ideas', 'contact_notes', 'contact_calls',
            ];
            foreach ($hasMany as $table) {
                DB::table($table)->where('contact_id', $loser->id)->update(['contact_id' => $kept->id]);
            }

            // Pivot: union of contact_group_id sets, deduped via sync.
            $groupIds = $kept->contactGroups()->pluck('contact_groups.id')
                ->merge($loser->contactGroups()->pluck('contact_groups.id'))
                ->unique()
                ->values()
                ->all();
            $loser->contactGroups()->detach();
            $kept->contactGroups()->sync($groupIds);

            // Loser's image string is set to null already (handled above) so
            // delete() doesn't accidentally orphan a file we kept.
            $loser->delete();
        });

        return redirect()->route('contacts.show', ['contact' => $kept->ulid])
            ->with('alert-success', __('duplicates.merged'));
    }

    private function relationsForCompare(): array
    {
        return [
            'urls', 'numbers', 'emails', 'dates', 'addresses',
            'comments', 'giftIdeas', 'notes', 'calls', 'contactGroups',
        ];
    }

    private function shapeForCompare(Contact $contact): array
    {
        $base = $contact->only([
            'ulid', 'firstname', 'lastname', 'title', 'title_after',
            'date_of_birth', 'iban', 'salutation', 'gender_id', 'company',
            'vatin', 'department', 'job', 'custom_id', 'nickname', 'active',
            'first_met', 'note', 'died_at', 'died_from', 'nationality_id',
            'image', 'created_at', 'updated_at',
        ]);

        return array_merge($base, [
            'fullname' => $contact->fullname,
            'urls' => $contact->urls->map->only(['name', 'url'])->all(),
            'numbers' => $contact->numbers->map->only(['name', 'number'])->all(),
            'emails' => $contact->emails->map->only(['name', 'email'])->all(),
            'dates' => $contact->dates->map->only(['name', 'date'])->all(),
            'addresses' => $contact->addresses->map->only(['name', 'street', 'zip', 'city', 'country_id'])->all(),
            'comments_count' => $contact->comments->count(),
            'gift_ideas_count' => $contact->giftIdeas->count(),
            'notes_count' => $contact->notes->count(),
            'calls_count' => $contact->calls->count(),
            'contact_groups' => $contact->contactGroups->map->only(['id', 'name'])->all(),
        ]);
    }

    private function reconcileImage(Contact $kept, Contact $loser, ?string $choice): void
    {
        $unlink = function (?string $filename) {
            if (! $filename) {
                return;
            }
            $path = storage_path('app/public/') . $filename;
            if (file_exists($path)) {
                @unlink($path);
            }
        };

        if ($choice === 'right') {
            // Keeping the loser's image — drop the kept's old image file.
            if ($kept->image && $kept->image !== $loser->image) {
                $unlink($kept->image);
            }
            $kept->image = $loser->image;
            $loser->image = null; // prevents accidental future cleanup of the file
        } elseif ($choice === 'left') {
            // Keeping the kept's image — drop the loser's image file.
            if ($loser->image && $loser->image !== $kept->image) {
                $unlink($loser->image);
            }
            $loser->image = null;
        }
        // null choice = neither has an image (validation passed because both empty).
    }
}
