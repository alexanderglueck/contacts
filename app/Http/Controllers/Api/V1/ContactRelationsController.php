<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\HandlesContactRelations;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactRelationRequest;
use App\Http\Requests\Api\V1\UpdateContactRelationRequest;
use App\Models\Contact;
use App\Models\ContactRelation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ContactRelationsController extends Controller
{
    use HandlesContactRelations;

    protected ?string $accessEntity = 'relations';

    public function index(Contact $contact): JsonResponse
    {
        $this->can('view');

        return response()->json([
            'data' => $contact->relationEntries()->map(fn (array $entry) => [
                'ulid' => $entry['relation']->ulid,
                'label' => $entry['label'],
                'inverse' => $entry['inverse'],
                'contact' => [
                    'ulid' => $entry['contact']->ulid,
                    'fullname' => $entry['contact']->fullname,
                ],
            ])->values(),
        ]);
    }

    public function show(Contact $contact, ContactRelation $relation): JsonResponse
    {
        $this->can('view');
        $this->verifyInvolves($contact, $relation);

        return response()->json(['data' => $this->serialize($relation, $contact)]);
    }

    public function store(StoreContactRelationRequest $request, Contact $contact): JsonResponse
    {
        $this->can('create');

        $other = Contact::where('ulid', $request->input('related_contact'))->firstOrFail();

        $relation = ContactRelation::linkBetween(
            $contact,
            $other,
            $request->input('forward_label'),
            $this->inverseLabel($request),
        );

        return response()->json(['data' => $this->serialize($relation, $contact)], 201);
    }

    public function update(UpdateContactRelationRequest $request, Contact $contact, ContactRelation $relation): JsonResponse
    {
        $this->can('edit');
        $this->verifyInvolves($contact, $relation);

        $relation->applyLabelsFrom(
            $contact,
            $request->input('forward_label'),
            $this->inverseLabel($request),
        );

        return response()->json(['data' => $this->serialize($relation, $contact)]);
    }

    public function destroy(Contact $contact, ContactRelation $relation): Response
    {
        $this->can('delete');
        $this->verifyInvolves($contact, $relation);

        $relation->delete();

        return response()->noContent();
    }

    /**
     * Serialize a relation oriented to the given contact's point of view.
     */
    private function serialize(ContactRelation $relation, Contact $perspective): array
    {
        $relation->loadMissing(['contactA', 'contactB']);
        $isA = $relation->contact_a_id === $perspective->id;
        $other = $isA ? $relation->contactB : $relation->contactA;

        return [
            'ulid' => $relation->ulid,
            'label' => $isA ? $relation->a_to_b_label : $relation->b_to_a_label,
            'inverse' => $isA ? $relation->b_to_a_label : $relation->a_to_b_label,
            'contact' => [
                'ulid' => $other->ulid,
                'fullname' => $other->fullname,
            ],
        ];
    }
}
