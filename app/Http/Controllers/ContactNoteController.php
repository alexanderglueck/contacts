<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactNote\ContactNoteStoreRequest;
use App\Http\Requests\ContactNote\ContactNoteUpdateRequest;
use App\Models\Contact;
use App\Models\ContactNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class ContactNoteController extends Controller
{
    protected ?string $accessEntity = 'notes';

    public function store(ContactNoteStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->notes()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_note.created'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_note.not_created'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function update(ContactNoteUpdateRequest $request, Contact $contact, ContactNote $contactNote): RedirectResponse
    {
        if ($contactNote->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_note.updated'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_note.not_updated'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function destroy(Contact $contact, ContactNote $contactNote): RedirectResponse
    {
        $this->can('delete');

        if ($contactNote->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_note.deleted'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_note.not_deleted'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }
}
