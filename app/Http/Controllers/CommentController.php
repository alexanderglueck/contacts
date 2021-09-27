<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Contact;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Comment\StoreCommentRequest;
use App\Http\Requests\Comment\DeleteCommentRequest;
use App\Http\Requests\Comment\UpdateCommentRequest;

class CommentController extends Controller
{
    protected $accessEntity = 'comments';

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreCommentRequest $request
     * @param Contact             $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreCommentRequest $request, Contact $contact)
    {
        if ($contact->comments()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.comment.created'));

            return redirect()->route('contacts.show', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.comment.not_created'));

            return redirect()->route('contacts.show', [$contact->slug]);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param Contact $contact
     * @param Comment $comment
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact, Comment $comment)
    {
        $this->can('edit');

        return view('comment.edit', [
            'contact' => $contact,
            'comment' => $comment
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateCommentRequest $request
     * @param Contact              $contact
     * @param Comment              $comment
     *
     * @return \Response
     */
    public function update(UpdateCommentRequest $request, Contact $contact, Comment $comment)
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
     *
     * @param DeleteCommentRequest $request
     * @param Contact              $contact
     * @param Comment              $comment
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(DeleteCommentRequest $request, Contact $contact, Comment $comment)
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
