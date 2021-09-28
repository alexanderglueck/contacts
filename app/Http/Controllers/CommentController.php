<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Contact;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\DeleteCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;

class CommentController extends Controller
{
    protected ?string $accessEntity = 'comments';

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommentRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->comments()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.comment.created'));

            return redirect()->route('contacts.show', [$contact->slug]);
        }

        Session::flash('alert-danger', trans('flash_message.comment.not_created'));

        return redirect()->route('contacts.show', [$contact->slug]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact, Comment $comment): View
    {
        $this->can('edit');

        return view('comment.edit', [
            'contact' => $contact,
            'comment' => $comment
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommentRequest $request, Contact $contact, Comment $comment): RedirectResponse
    {
        if ($comment->update($request->validated())) {
            Session::flash('alert-success', trans('flash_message.comment.updated'));

            return redirect()->route('contacts.show', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.comment.not_updated'));

            return redirect()->route('comments.edit', [$contact->slug, $comment->id]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteCommentRequest $request, Contact $contact, Comment $comment): RedirectResponse
    {
        if ($comment->delete()) {
            Session::flash('alert-success', trans('flash_message.comment.deleted'));

            return redirect()->route('contacts.show', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.comment.not_deleted'));

            return redirect()->route('comments.edit', [$contact->slug, $comment->id]);
        }
    }
}
