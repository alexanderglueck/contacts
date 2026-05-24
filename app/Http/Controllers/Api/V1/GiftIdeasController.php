<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreGiftIdeaRequest;
use App\Http\Requests\Api\V1\UpdateGiftIdeaRequest;
use App\Models\Contact;
use App\Models\GiftIdea;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class GiftIdeasController extends Controller
{
    protected ?string $accessEntity = 'giftIdeas';

    public function store(StoreGiftIdeaRequest $request, Contact $contact): JsonResponse
    {
        $this->can('create');

        $gift = $contact->giftIdeas()->create($request->validated());

        return response()->json(['data' => $this->serialize($gift)], 201);
    }

    public function update(UpdateGiftIdeaRequest $request, Contact $contact, GiftIdea $gift_idea): JsonResponse
    {
        $this->can('edit');

        $gift_idea->fill($request->validated())->save();

        return response()->json(['data' => $this->serialize($gift_idea)]);
    }

    public function destroy(Contact $contact, GiftIdea $gift_idea): Response
    {
        $this->can('delete');

        $gift_idea->delete();

        return response()->noContent();
    }

    private function serialize(GiftIdea $g): array
    {
        return [
            'ulid' => $g->ulid,
            'name' => $g->name,
            'description' => $g->description,
            'url' => $g->url,
            'due_at' => $g->due_at,
        ];
    }
}
