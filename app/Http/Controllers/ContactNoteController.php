<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactNote;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ContactNoteController extends Controller
{
    protected $accessEntity = 'notes';

    private $validationRules = [
        'name' => 'required',
        'note' => 'required'
    ];

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Contact $contact)
    {
        $this->can('view');

        return view('contact_note.index', [
            'contact' => $contact,
            'notes' => $contact->notes
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Contact $contact)
    {
        $this->can('create');

        return view('contact_note.create', [
            'contact' => $contact,
            'contactNote' => new ContactNote()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Contact      $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Contact $contact)
    {
        $this->can('create');

        $this->validate($request, $this->validationRules);

        $contactNote = new ContactNote();
        $contactNote->fill($request->all());
        $contactNote->contact_id = $contact->id;
        $contactNote->created_by = Auth::id();
        $contactNote->updated_by = Auth::id();

        if ($contactNote->save()) {
            Session::flash('alert-success', trans('flash_message.contact_note.created'));

            return redirect()->route('contact_notes.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_note.not_created'));

            return redirect()->route('contact_notes.create', [$contact->slug]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contact     $contact
     * @param  \App\Models\ContactNote $contactNote
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact, ContactNote $contactNote)
    {
        $this->can('view');

        return view('contact_note.show', [
            'contact' => $contact,
            'contactNote' => $contactNote
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contact     $contact
     * @param  \App\Models\ContactNote $contactNote
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact, ContactNote $contactNote)
    {
        $this->can('edit');

        return view('contact_note.edit', [
            'contact' => $contact,
            'contactNote' => $contactNote,
            'createButtonText' => 'Note updated'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Contact      $contact
     * @param  \App\Models\ContactNote  $contactNote
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact, ContactNote $contactNote)
    {
        $this->can('edit');

        $this->validate($request, $this->validationRules);

        $contactNote->fill($request->all());
        $contactNote->updated_by = Auth::id();

        if ($contactNote->save()) {
            Session::flash('alert-success', trans('flash_message.contact_note.updated'));

            return redirect()->route('contact_notes.show', [$contact->slug, $contactNote->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_note.not_updated'));

            return redirect()->route('contact_notes.edit', [$contact->slug, $contactNote->slug]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contact     $contact
     * @param  \App\Models\ContactNote $contactNote
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Contact $contact, ContactNote $contactNote)
    {
        $this->can('delete');

        if ($contactNote->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_note.deleted'));

            return redirect()->route('contact_notes.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_note.not_deleted'));

            return redirect()->route('contact_notes.delete', [$contact->slug, $contactNote->slug]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  \App\Models\Contact     $contact
     * @param  \App\Models\ContactNote $contactNote
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Contact $contact, ContactNote $contactNote)
    {
        $this->can('delete');

        return view('contact_note.delete', [
            'contact' => $contact,
            'contactNote' => $contactNote
        ]);
    }
}
