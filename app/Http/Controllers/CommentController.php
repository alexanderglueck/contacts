<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\DeleteCommentRequest;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CommentController extends Controller
{
    protected ?string $accessEntity = 'comments';

    public function store(StoreCommentRequest $request, Contact $contact): RedirectResponse
    {
        $parentId = null;
        if ($request->filled('parent_ulid')) {
            // Resolve the parent within this contact's thread, so a malicious
            // parent_ulid from another contact can't be smuggled in.
            $parentId = $contact->comments()
                ->where('ulid', $request->input('parent_ulid'))
                ->value('id');
        }

        $created = $contact->comments()->create([
            'comment' => $request->input('comment'),
            'parent_id' => $parentId,
        ]);

        Session::flash(
            $created ? 'alert-success' : 'alert-danger',
            trans($created ? 'flash_message.comment.created' : 'flash_message.comment.not_created'),
        );

        return redirect()->route('contacts.show', [$contact->ulid]);
    }

    public function update(UpdateCommentRequest $request, Contact $contact, Comment $comment): RedirectResponse
    {
        Session::flash(
            $comment->update($request->validated()) ? 'alert-success' : 'alert-danger',
            trans($comment->wasChanged() ? 'flash_message.comment.updated' : 'flash_message.comment.not_updated'),
        );

        return redirect()->route('contacts.show', [$contact->ulid]);
    }

    /**
     * Delete a comment, but if it still has replies, tombstone it instead
     * — the thread needs the parent row so the children can keep rendering
     * underneath. After a hard-delete (leaf removal), walk up the parent
     * chain and hard-delete any tombstoned ancestor that no longer has
     * children. So the only way a tombstoned row sticks around is while
     * it's still carrying replies.
     */
    public function destroy(DeleteCommentRequest $request, Contact $contact, Comment $comment): RedirectResponse
    {
        $ok = DB::transaction(function () use ($comment) {
            if ($comment->replies()->exists()) {
                // Tombstone — keep row so children still render.
                return $comment->update([
                    'comment' => null,
                    'tombstoned_at' => now(),
                ]);
            }

            // Leaf — hard delete, then walk up cleaning tombstoned ancestors.
            $parent = $comment->parent;
            $deleted = $comment->delete();
            $this->cleanupAncestors($parent);

            return $deleted;
        });

        Session::flash(
            $ok ? 'alert-success' : 'alert-danger',
            trans($ok ? 'flash_message.comment.deleted' : 'flash_message.comment.not_deleted'),
        );

        return redirect()->route('contacts.show', [$contact->ulid]);
    }

    private function cleanupAncestors(?Comment $parent): void
    {
        while ($parent && $parent->isTombstoned() && ! $parent->replies()->exists()) {
            $grandparent = $parent->parent;
            $parent->delete();
            $parent = $grandparent;
        }
    }
}
