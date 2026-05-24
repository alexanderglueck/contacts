<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactNumberRequest;
use App\Http\Requests\Api\V1\UpdateContactNumberRequest;
use App\Models\Contact;
use App\Models\ContactNumber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

/**
 * CRUD for a contact's phone numbers. The {contact}/{number} route binding
 * is scoped (->scopeBindings() in api.php), so Laravel guarantees the
 * resolved ContactNumber actually belongs to the resolved Contact — a
 * "01ABC.../numbers/01XYZ..." path where xyz belongs to a different
 * contact 404s before reaching the controller.
 */
class ContactNumbersController extends Controller
{
    protected ?string $accessEntity = 'numbers';

    public function index(Contact $contact): JsonResponse
    {
        $this->can('view');

        return response()->json([
            'data' => $contact->numbers->map(fn (ContactNumber $n) => $this->serialize($n))->values(),
        ]);
    }

    public function show(Contact $contact, ContactNumber $number): JsonResponse
    {
        $this->can('view');

        return response()->json(['data' => $this->serialize($number)]);
    }

    public function store(StoreContactNumberRequest $request, Contact $contact): JsonResponse
    {
        $this->can('create');

        $number = $contact->numbers()->create($request->validated());

        return response()->json(['data' => $this->serialize($number)], 201);
    }

    public function update(UpdateContactNumberRequest $request, Contact $contact, ContactNumber $number): JsonResponse
    {
        $this->can('edit');

        $number->fill($request->validated())->save();

        return response()->json(['data' => $this->serialize($number)]);
    }

    public function destroy(Contact $contact, ContactNumber $number): Response
    {
        $this->can('delete');

        $number->delete();

        return response()->noContent();
    }

    private function serialize(ContactNumber $n): array
    {
        return [
            'ulid' => $n->ulid,
            'name' => $n->name,
            'number' => $n->number,
            // Canonical E.164 form for client-side matching / queries.
            // Null if the stored number couldn't be parsed (legacy data
            // or weirdly malformed input that slipped through).
            'e164' => $n->e164,
        ];
    }
}
