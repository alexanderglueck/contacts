<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactNoteRequest;
use App\Http\Requests\Api\V1\UpdateContactNoteRequest;
use App\Models\Contact;
use App\Models\ContactNote;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ContactNotesController extends Controller
{
    protected ?string $accessEntity = 'notes';

    public function store(StoreContactNoteRequest $request, Contact $contact): JsonResponse
    {
        $this->can('create');

        $note = $contact->notes()->create($request->validated());

        return response()->json(['data' => $this->serialize($note)], 201);
    }

    public function update(UpdateContactNoteRequest $request, Contact $contact, ContactNote $note): JsonResponse
    {
        $this->can('edit');

        $note->fill($request->validated())->save();

        return response()->json(['data' => $this->serialize($note)]);
    }

    public function destroy(Contact $contact, ContactNote $note): Response
    {
        $this->can('delete');

        $note->delete();

        return response()->noContent();
    }

    private function serialize(ContactNote $n): array
    {
        return [
            'ulid' => $n->ulid,
            'name' => $n->name,
            'note' => $n->note,
        ];
    }
}
