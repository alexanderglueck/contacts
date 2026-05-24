<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactUrlRequest;
use App\Http\Requests\Api\V1\UpdateContactUrlRequest;
use App\Models\Contact;
use App\Models\ContactUrl;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ContactUrlsController extends Controller
{
    protected ?string $accessEntity = 'urls';

    public function index(Contact $contact): JsonResponse
    {
        $this->can('view');

        return response()->json([
            'data' => $contact->urls->map(fn (ContactUrl $u) => $this->serialize($u))->values(),
        ]);
    }

    public function show(Contact $contact, ContactUrl $url): JsonResponse
    {
        $this->can('view');

        return response()->json(['data' => $this->serialize($url)]);
    }

    public function store(StoreContactUrlRequest $request, Contact $contact): JsonResponse
    {
        $this->can('create');

        $url = $contact->urls()->create($request->validated());

        return response()->json(['data' => $this->serialize($url)], 201);
    }

    public function update(UpdateContactUrlRequest $request, Contact $contact, ContactUrl $url): JsonResponse
    {
        $this->can('edit');

        $url->fill($request->validated())->save();

        return response()->json(['data' => $this->serialize($url)]);
    }

    public function destroy(Contact $contact, ContactUrl $url): Response
    {
        $this->can('delete');

        $url->delete();

        return response()->noContent();
    }

    private function serialize(ContactUrl $u): array
    {
        return [
            'ulid' => $u->ulid,
            'name' => $u->name,
            'url' => $u->url,
        ];
    }
}
