<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\DeleteCommentRequest;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;
use App\Models\Comment;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
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

    public function destroy(DeleteCommentRequest $request, Contact $contact, Comment $comment): RedirectResponse
    {
        Session::flash(
            $comment->delete() ? 'alert-success' : 'alert-danger',
            trans('flash_message.comment.deleted'),
        );

        return redirect()->route('contacts.show', [$contact->ulid]);
    }
}
