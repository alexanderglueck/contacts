<?php

namespace App\Http\Controllers;

use App\Http\Requests\MergeContactRequest;
use App\Models\Contact;
use App\Models\ContactNonDuplicate;
use App\Services\DuplicateContactDetector;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DuplicateContactController extends Controller
{
    public function index(): Response
    {
        $teamId = auth()->user()->current_team_id;
        $detector = new DuplicateContactDetector($teamId);

        $groups = $this->mergeGroupsBySameSet($detector->find());
        $groups = $this->filterByNonDuplicates($groups, $teamId);

        return Inertia::render('Contacts/Duplicates/Index', [
            'groups' => $groups,
        ]);
    }

    /**
     * Apply the user's "not a duplicate" marks. We keep a contact in a group
     * only if it still has at least one peer that *isn't* marked as
     * non-duplicate with it. Groups that drop below 2 contacts are removed.
     *
     * This handles 3+ member groups gracefully: marking pair (A,B) as
     * non-dup doesn't hide a group of {A,B,C} where (A,C) and (B,C) are
     * still suspicious — only (A,B)'s shared signal-row would disappear,
     * not the whole group.
     */
    private function filterByNonDuplicates(array $groups, int $teamId): array
    {
        $allIds = collect($groups)
            ->flatMap(fn ($g) => array_column($g['contacts'], 'id'))
            ->unique()
            ->values();

        if ($allIds->isEmpty()) {
            return $groups;
        }

        $nonDupPairs = ContactNonDuplicate::query()
            ->where('team_id', $teamId)
            ->whereIn('contact_a_id', $allIds)
            ->whereIn('contact_b_id', $allIds)
            ->get(['contact_a_id', 'contact_b_id']);

        // Flatten to a set keyed by "min:max" so lookup is O(1) regardless
        // of which order the user picked the pair.
        $nonDupSet = [];
        foreach ($nonDupPairs as $row) {
            $a = min($row->contact_a_id, $row->contact_b_id);
            $b = max($row->contact_a_id, $row->contact_b_id);
            $nonDupSet["{$a}:{$b}"] = true;
        }
        $pairKey = fn ($i, $j) => min($i, $j) . ':' . max($i, $j);

        $result = [];
        foreach ($groups as $group) {
            $ids = array_column($group['contacts'], 'id');
            $kept = [];
            foreach ($ids as $id) {
                foreach ($ids as $other) {
                    if ($id === $other) {
                        continue;
                    }
                    if (! isset($nonDupSet[$pairKey($id, $other)])) {
                        $kept[$id] = true;
                        break;
                    }
                }
            }
            $group['contacts'] = array_values(array_filter(
                $group['contacts'],
                fn ($c) => isset($kept[$c['id']]),
            ));
            if (count($group['contacts']) >= 2) {
                $result[] = $group;
            }
        }

        return $result;
    }

    /**
     * The detector produces one group per signal. When the exact same set of
     * contacts shows up under more than one signal (e.g. two contacts share
     * both an email AND a phone number), collapse them into a single group
     * carrying every signal that matched. Otherwise users see what looks
     * like the same list 2–4 times in a row.
     *
     * Set identity = the unordered set of contact ULIDs. Different signals
     * with overlapping-but-not-identical contact sets stay separate, since
     * dropping one would hide a real picking option.
     */
    private function mergeGroupsBySameSet(array $groups): array
    {
        $byKey = [];

        foreach ($groups as $group) {
            $ulids = array_map(fn ($c) => $c['ulid'], $group['contacts']);
            sort($ulids);
            $key = implode('|', $ulids);

            if (! isset($byKey[$key])) {
                $byKey[$key] = [
                    'signals' => [],
                    'contacts' => $group['contacts'],
                ];
            }
            $byKey[$key]['signals'][] = [
                'type' => $group['signal'],
                'value' => $group['value'],
            ];
        }

        return array_values($byKey);
    }

    /**
     * Autocomplete endpoint for the "merge arbitrary contacts" picker on the
     * Duplicates index page. SQL LIKE rather than Scout so the picker keeps
     * working even when Meilisearch is unhappy — and the per-tenant rowcount
     * makes the difference irrelevant.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim((string) $request->get('q', ''));

        if ($q === '') {
            return response()->json([]);
        }

        $teamId = auth()->user()->current_team_id;
        $like = '%' . addcslashes($q, '%_\\') . '%';

        $matches = Contact::query()
            ->withoutGlobalScopes()
            ->where('team_id', $teamId)
            ->where(function ($qb) use ($like) {
                $qb->where('firstname', 'like', $like)
                    ->orWhere('lastname', 'like', $like)
                    ->orWhere('company', 'like', $like)
                    ->orWhere('nickname', 'like', $like);
            })
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->limit(15)
            ->get(['ulid', 'firstname', 'lastname', 'company', 'date_of_birth']);

        return response()->json($matches->map(function (Contact $c) {
            $parts = [trim(($c->firstname ?? '') . ' ' . ($c->lastname ?? ''))];
            if ($c->company) {
                $parts[] = $c->company;
            }
            if ($c->date_of_birth) {
                $parts[] = $c->date_of_birth;
            }

            return [
                'ulid' => $c->ulid,
                'label' => implode(' · ', array_filter($parts)),
            ];
        }));
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
            'fieldLabels' => collect(MergeContactRequest::FIELDS)
                ->mapWithKeys(function ($field) {
                    $label = __('duplicates.fields.' . $field);
                    if ($label === 'duplicates.fields.' . $field) {
                        $label = $field;
                    }

                    return [$field => $label];
                })
                ->all(),
        ]);
    }

    /**
     * Record this pair as "not actually a duplicate" so future detector runs
     * stop suggesting them. Pair stored normalised (smaller id first) so the
     * unique index catches it regardless of which order the user clicked.
     */
    public function markNotDuplicate(Request $request): RedirectResponse
    {
        $request->validate([
            'left_ulid' => ['required', 'string'],
            'right_ulid' => ['required', 'string', 'different:left_ulid'],
        ]);

        $teamId = auth()->user()->current_team_id;

        $left = Contact::where('ulid', $request->input('left_ulid'))->firstOrFail();
        $right = Contact::where('ulid', $request->input('right_ulid'))->firstOrFail();

        if ($left->team_id !== $teamId || $right->team_id !== $teamId) {
            abort(403);
        }

        $a = min($left->id, $right->id);
        $b = max($left->id, $right->id);

        ContactNonDuplicate::updateOrCreate(
            [
                'team_id' => $teamId,
                'contact_a_id' => $a,
                'contact_b_id' => $b,
            ],
            [
                'created_by' => auth()->id(),
            ],
        );

        return redirect()->route('duplicates.index')
            ->with('alert-success', __('duplicates.marked_not_duplicate'));
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

            // Per-row sub-resource decisions. Default for an unspecified id is
            // "keep" (mirrors the old union behavior); only explicit `false`
            // drops the row. The relations->table mapping lives in one place
            // so the wire format stays decoupled from physical table names.
            $relations = [
                'urls' => 'contact_urls',
                'numbers' => 'contact_numbers',
                'emails' => 'contact_emails',
                'dates' => 'contact_dates',
                'addresses' => 'contact_addresses',
                'comments' => 'comments',
                'giftIdeas' => 'gift_ideas',
                'notes' => 'contact_notes',
                'calls' => 'contact_calls',
            ];

            $picks = (array) $request->input('subResources', []);

            foreach ($relations as $relationKey => $table) {
                $decisions = (array) ($picks[$relationKey] ?? []);

                $ids = DB::table($table)
                    ->whereIn('contact_id', [$kept->id, $loser->id])
                    ->pluck('id');

                $dropIds = [];
                $keepIds = [];
                foreach ($ids as $id) {
                    // PHP-side normalisation: form posts come back as strings/bools.
                    $decision = $decisions[(string) $id] ?? $decisions[$id] ?? null;
                    if ($decision === false || $decision === 'false' || $decision === 0 || $decision === '0') {
                        $dropIds[] = $id;
                    } else {
                        $keepIds[] = $id;
                    }
                }

                if (! empty($dropIds)) {
                    DB::table($table)->whereIn('id', $dropIds)->delete();
                }
                if (! empty($keepIds)) {
                    DB::table($table)->whereIn('id', $keepIds)->update(['contact_id' => $kept->id]);
                }
            }

            // Pivot: same decision model. Union of group ids from both contacts
            // is filtered by the user's checkboxes, then synced to the kept
            // contact. Detaching the loser first lets sync replace cleanly.
            $unionGroupIds = $kept->contactGroups()->pluck('contact_groups.id')
                ->merge($loser->contactGroups()->pluck('contact_groups.id'))
                ->unique()
                ->values();
            $groupDecisions = (array) ($picks['contactGroups'] ?? []);
            $finalGroupIds = $unionGroupIds->filter(function ($gid) use ($groupDecisions) {
                $decision = $groupDecisions[(string) $gid] ?? $groupDecisions[$gid] ?? null;

                return ! ($decision === false || $decision === 'false' || $decision === 0 || $decision === '0');
            })->values()->all();

            $loser->contactGroups()->detach();
            $kept->contactGroups()->sync($finalGroupIds);

            // Loser's image string is set to null already (handled above) so
            // delete() doesn't accidentally orphan a file we kept.
            $loser->delete();
        });

        // Land back on the duplicates index — typical workflow is to
        // process a batch of duplicates in one sitting, so jumping into
        // the kept contact's detail page interrupts that flow.
        return redirect()->route('duplicates.index')
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
            'urls' => $contact->urls->map->only(['id', 'name', 'url'])->values()->all(),
            'numbers' => $contact->numbers->map->only(['id', 'name', 'number'])->values()->all(),
            'emails' => $contact->emails->map->only(['id', 'name', 'email'])->values()->all(),
            'dates' => $contact->dates->map->only(['id', 'name', 'date'])->values()->all(),
            'addresses' => $contact->addresses->map(fn ($a) => [
                'id' => $a->id,
                'name' => $a->name,
                'street' => $a->street,
                'zip' => $a->zip,
                'city' => $a->city,
            ])->values()->all(),
            // Send full bodies; the Compare page line-clamps for layout and exposes
            // the full text on hover via `title`.
            'comments' => $contact->comments->map(fn ($c) => [
                'id' => $c->id,
                'body' => $c->comment ?: '—',
            ])->values()->all(),
            'giftIdeas' => $contact->giftIdeas->map(fn ($g) => [
                'id' => $g->id,
                'name' => $g->name,
            ])->values()->all(),
            'notes' => $contact->notes->map(fn ($n) => [
                'id' => $n->id,
                'body' => $n->note ?: '—',
            ])->values()->all(),
            'calls' => $contact->calls->map(fn ($c) => [
                'id' => $c->id,
                'when' => $c->called_at ?? $c->created_at,
                'body' => $c->note ?: '—',
            ])->values()->all(),
            'contactGroups' => $contact->contactGroups->map->only(['id', 'name'])->values()->all(),
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
