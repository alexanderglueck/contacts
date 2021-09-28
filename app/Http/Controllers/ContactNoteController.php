<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactNote;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\ContactNote\ContactNoteStoreRequest;
use App\Http\Requests\ContactNote\ContactNoteUpdateRequest;

class ContactNoteController extends Controller
{
    protected ?string $accessEntity = 'notes';

    /**
     * Display a listing of the resource.
     */
    public function index(Contact $contact): View
    {
        $this->can('view');

        return view('contact_note.index', [
            'contact' => $contact,
            'notes' => $contact->notes
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Contact $contact): View
    {
        $this->can('create');

        return view('contact_note.create', [
            'contact' => $contact,
            'contactNote' => new ContactNote()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactNoteStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->notes()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_note.created'));

            return redirect()->route('contact_notes.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_note.not_created'));

            return redirect()->route('contact_notes.create', [$contact->slug]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact, ContactNote $contactNote): View
    {
        $this->can('view');

        return view('contact_note.show', [
            'contact' => $contact,
            'contactNote' => $contactNote
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact, ContactNote $contactNote): View
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
     */
    public function update(ContactNoteUpdateRequest $request, Contact $contact, ContactNote $contactNote): RedirectResponse
    {
        if ($contactNote->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_note.updated'));

            return redirect()->route('contact_notes.show', [$contact->slug, $contactNote->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_note.not_updated'));

            return redirect()->route('contact_notes.edit', [$contact->slug, $contactNote->slug]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact, ContactNote $contactNote): RedirectResponse
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
     */
    public function delete(Contact $contact, ContactNote $contactNote): View
    {
        $this->can('delete');

        return view('contact_note.delete', [
            'contact' => $contact,
            'contactNote' => $contactNote
        ]);
    }
}
