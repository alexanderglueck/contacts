<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactCallRequest;
use App\Http\Requests\Api\V1\UpdateContactCallRequest;
use App\Models\Contact;
use App\Models\ContactCall;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ContactCallsController extends Controller
{
    protected ?string $accessEntity = 'calls';

    public function store(StoreContactCallRequest $request, Contact $contact): JsonResponse
    {
        $this->can('create');

        $call = $contact->calls()->create($request->validated());

        return response()->json(['data' => $this->serialize($call)], 201);
    }

    public function update(UpdateContactCallRequest $request, Contact $contact, ContactCall $call): JsonResponse
    {
        $this->can('edit');

        $call->fill($request->validated())->save();

        return response()->json(['data' => $this->serialize($call)]);
    }

    public function destroy(Contact $contact, ContactCall $call): Response
    {
        $this->can('delete');

        $call->delete();

        return response()->noContent();
    }

    private function serialize(ContactCall $c): array
    {
        return [
            'ulid' => $c->ulid,
            'called_at' => $c->called_at ? substr($c->called_at, 0, 16) : null,
            'note' => $c->note,
        ];
    }
}
