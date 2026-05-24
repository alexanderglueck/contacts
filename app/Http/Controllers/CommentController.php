<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\DeleteCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;

class CommentController extends Controller
{
    protected ?string $accessEntity = 'comments';

    public function store(StoreCommentRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->comments()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.comment.created'));

            return redirect()->route('contacts.show', [$contact->ulid]);
        }

        Session::flash('alert-danger', trans('flash_message.comment.not_created'));

        return redirect()->route('contacts.show', [$contact->ulid]);
    }

    public function edit(Contact $contact, Comment $comment): Response
    {
        $this->can('edit');

        return Inertia::render('Comments/Edit', [
            'contact' => [
                'id' => $contact->id,
                'ulid' => $contact->ulid,
                'fullname' => $contact->fullname,
            ],
            'comment' => [
                'ulid' => $comment->ulid,
                'comment' => $comment->comment,
            ],
        ]);
    }

    public function update(UpdateCommentRequest $request, Contact $contact, Comment $comment): RedirectResponse
    {
        if ($comment->update($request->validated())) {
            Session::flash('alert-success', trans('flash_message.comment.updated'));

            return redirect()->route('contacts.show', [$contact->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.comment.not_updated'));

            return redirect()->route('comments.edit', [$contact->ulid, $comment->ulid]);
        }
    }

    public function destroy(DeleteCommentRequest $request, Contact $contact, Comment $comment): RedirectResponse
    {
        if ($comment->delete()) {
            Session::flash('alert-success', trans('flash_message.comment.deleted'));

            return redirect()->route('contacts.show', [$contact->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.comment.not_deleted'));

            return redirect()->route('comments.edit', [$contact->ulid, $comment->ulid]);
        }
    }
}
