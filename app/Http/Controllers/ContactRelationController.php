<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesContactRelations;
use App\Http\Requests\ContactRelation\ContactRelationStoreRequest;
use App\Http\Requests\ContactRelation\ContactRelationUpdateRequest;
use App\Models\Contact;
use App\Models\ContactRelation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ContactRelationController extends Controller
{
    use HandlesContactRelations;

    protected ?string $accessEntity = 'relations';

    public function store(ContactRelationStoreRequest $request, Contact $contact): RedirectResponse
    {
        $other = Contact::where('ulid', $request->input('related_contact'))->firstOrFail();

        ContactRelation::linkBetween(
            $contact,
            $other,
            $request->input('forward_label'),
            $this->inverseLabel($request),
        );

        Session::flash('alert-success', trans('flash_message.contact_relation.created'));

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function update(ContactRelationUpdateRequest $request, Contact $contact, ContactRelation $contactRelation): RedirectResponse
    {
        $this->verifyInvolves($contact, $contactRelation);

        $contactRelation->applyLabelsFrom(
            $contact,
            $request->input('forward_label'),
            $this->inverseLabel($request),
        );

        Session::flash('alert-success', trans('flash_message.contact_relation.updated'));

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function destroy(Contact $contact, ContactRelation $contactRelation): RedirectResponse
    {
        $this->can('delete');
        $this->verifyInvolves($contact, $contactRelation);

        if ($contactRelation->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_relation.deleted'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_relation.not_deleted'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    /**
     * Typeahead for the relation create form: tenant-scoped contacts matching
     * the query, excluding this contact and any already linked to it.
     */
    public function search(Request $request, Contact $contact): JsonResponse
    {
        $this->can('view');

        $term = trim((string) $request->query('q', ''));

        $excludeIds = ContactRelation::where(function ($query) use ($contact) {
            $query->where('contact_a_id', $contact->id)->orWhere('contact_b_id', $contact->id);
        })->get(['contact_a_id', 'contact_b_id'])
            ->flatMap(fn (ContactRelation $r) => [$r->contact_a_id, $r->contact_b_id])
            ->push($contact->id)
            ->unique()
            ->all();

        $query = Contact::sorted()->active()->whereNotIn('id', $excludeIds);

        if ($term !== '') {
            $query->where(function ($builder) use ($term) {
                $like = '%' . $term . '%';
                $builder->where('firstname', 'like', $like)
                    ->orWhere('lastname', 'like', $like)
                    ->orWhere('nickname', 'like', $like)
                    ->orWhere('company', 'like', $like);
            });
        }

        $contacts = $query->limit(10)->get();

        return response()->json([
            'data' => $contacts->map(fn (Contact $c) => [
                'ulid' => $c->ulid,
                'fullname' => $c->fullname,
            ])->values(),
        ]);
    }
}
