<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactEmailRequest;
use App\Http\Requests\Api\V1\UpdateContactEmailRequest;
use App\Models\Contact;
use App\Models\ContactEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ContactEmailsController extends Controller
{
    protected ?string $accessEntity = 'emails';

    public function index(Contact $contact): JsonResponse
    {
        $this->can('view');

        return response()->json([
            'data' => $contact->emails->map(fn (ContactEmail $e) => $this->serialize($e))->values(),
        ]);
    }

    public function show(Contact $contact, ContactEmail $email): JsonResponse
    {
        $this->can('view');

        return response()->json(['data' => $this->serialize($email)]);
    }

    public function store(StoreContactEmailRequest $request, Contact $contact): JsonResponse
    {
        $this->can('create');

        $email = $contact->emails()->create($request->validated());

        return response()->json(['data' => $this->serialize($email)], 201);
    }

    public function update(UpdateContactEmailRequest $request, Contact $contact, ContactEmail $email): JsonResponse
    {
        $this->can('edit');

        $email->fill($request->validated())->save();

        return response()->json(['data' => $this->serialize($email)]);
    }

    public function destroy(Contact $contact, ContactEmail $email): Response
    {
        $this->can('delete');

        $email->delete();

        return response()->noContent();
    }

    private function serialize(ContactEmail $e): array
    {
        return [
            'ulid' => $e->ulid,
            'name' => $e->name,
            'email' => $e->email,
        ];
    }
}
