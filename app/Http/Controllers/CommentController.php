<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Comment;
use App\Models\Contact;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Comment\StoreComment;
use App\Http\Requests\Comment\DeleteComment;
use App\Http\Requests\Comment\UpdateComment;

class CommentController extends Controller
{
    protected $accessEntity = 'comments';

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreComment $request
     * @param Contact      $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function store(StoreComment $request, Contact $contact)
    {
        $comment = new Comment();
        $comment->fill($request->all());

        if ($request->has('parent_id')) {
            $comment->parent_id = $request->parent_id;
        }

        $comment->contact_id = $contact->id;
        $comment->created_by = Auth::id();
        $comment->save();

        if ($comment->save()) {
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
     * @param UpdateComment $request
     * @param Contact       $contact
     * @param Comment       $comment
     *
     * @return \Response
     */
    public function update(UpdateComment $request, Contact $contact, Comment $comment)
    {
        $comment->comment = $request->comment;

        if ($comment->save()) {
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
     * @param DeleteComment $request
     * @param Contact       $contact
     * @param Comment       $comment
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(DeleteComment $request, Contact $contact, Comment $comment)
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
