<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactCommentRequest;
use App\Http\Requests\Api\V1\UpdateContactCommentRequest;
use App\Models\Comment;
use App\Models\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class ContactCommentsController extends Controller
{
    protected ?string $accessEntity = 'comments';

    /**
     * Return the contact's comments as a flat list, ordered by id ASC so
     * the parent of any row always appears before its children. The client
     * builds the tree itself from `parent_ulid` — keeps the API stable
     * regardless of how the UI wants to render threads.
     *
     * Tombstoned rows are included (with `comment = null`, `tombstoned =
     * true`) so reply chains remain renderable beneath a deleted parent.
     */
    public function index(Contact $contact): JsonResponse
    {
        $this->can('view');

        $comments = $contact->comments()
            ->with(['owner:id,name', 'parent:id,ulid'])
            ->orderBy('id')
            ->get();

        return response()->json([
            'data' => $comments->map(fn (Comment $c) => $this->serialize($c))->values(),
        ]);
    }

    public function show(Contact $contact, Comment $comment): JsonResponse
    {
        $this->can('view');

        $comment->loadMissing(['owner:id,name', 'parent:id,ulid']);

        return response()->json(['data' => $this->serialize($comment)]);
    }

    public function store(StoreContactCommentRequest $request, Contact $contact): JsonResponse
    {
        $this->can('create');

        $parentId = null;
        if ($request->filled('parent_ulid')) {
            // Resolve parent within this contact's thread only — prevents
            // smuggling a parent_ulid from another contact's tree.
            $parentId = $contact->comments()
                ->where('ulid', $request->input('parent_ulid'))
                ->value('id');

            if ($parentId === null) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => ['parent_ulid' => ['Parent comment not found on this contact.']],
                ], 422);
            }
        }

        $comment = $contact->comments()->create([
            'comment' => $request->input('comment'),
            'parent_id' => $parentId,
        ]);

        $comment->load(['owner:id,name', 'parent:id,ulid']);

        return response()->json(['data' => $this->serialize($comment)], 201);
    }

    public function update(UpdateContactCommentRequest $request, Contact $contact, Comment $comment): JsonResponse
    {
        $this->can('edit');

        $comment->fill($request->validated())->save();
        $comment->load(['owner:id,name', 'parent:id,ulid']);

        return response()->json(['data' => $this->serialize($comment)]);
    }

    /**
     * Delete a comment, but if it still has replies, tombstone it instead
     * — the thread needs the parent row so children can keep rendering.
     * After a leaf hard-delete, walk up the parent chain and clean out
     * any tombstoned ancestor that no longer has children. Mirrors the
     * web CommentController behaviour.
     */
    public function destroy(Contact $contact, Comment $comment): Response
    {
        $this->can('delete');

        DB::transaction(function () use ($comment) {
            if ($comment->replies()->exists()) {
                $comment->update([
                    'comment' => null,
                    'tombstoned_at' => now(),
                ]);

                return;
            }

            $parent = $comment->parent;
            $comment->delete();
            $this->cleanupAncestors($parent);
        });

        return response()->noContent();
    }

    private function cleanupAncestors(?Comment $parent): void
    {
        while ($parent && $parent->isTombstoned() && ! $parent->replies()->exists()) {
            $grandparent = $parent->parent;
            $parent->delete();
            $parent = $grandparent;
        }
    }

    private function serialize(Comment $c): array
    {
        $tombstoned = $c->isTombstoned();

        return [
            'ulid' => $c->ulid,
            'parent_ulid' => $c->parent?->ulid,
            'comment' => $tombstoned ? null : $c->comment,
            'tombstoned' => $tombstoned,
            'owner' => $c->owner ? [
                'id' => $c->owner->id,
                'name' => $c->owner->name,
            ] : null,
            'created_at' => optional($c->created_at)->toIso8601String(),
            'updated_at' => optional($c->updated_at)->toIso8601String(),
        ];
    }

}
