<?php

namespace App\Http\Controllers;

use Auth;
use App\Models\Comment;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CommentController extends Controller
{
    protected $accessEntity = 'comments';

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Contact                   $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Contact $contact)
    {
        $this->can('create');

        $this->validate($request, [
            'comment' => 'required'
        ]);

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
     * @param Contact              $contact
     * @param  \App\Models\Comment $comment
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
     * @param  \Illuminate\Http\Request $request
     * @param Contact                   $contact
     * @param  \App\Models\Comment      $comment
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact, Comment $comment)
    {
        $this->can('edit');

        $this->validate($request, [
            'comment' => 'required'
        ]);

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
     * @param Contact              $contact
     * @param  \App\Models\Comment $comment
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Contact $contact, Comment $comment)
    {
        $this->can('delete');

        if ($comment->delete()) {
            Session::flash('alert-success', trans('flash_message.comment.deleted'));

            return redirect()->route('contacts.show', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.comment.not_deleted'));

            return redirect()->route('comments.edit', [$contact->slug, $comment->id]);
        }
    }
}
