<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactEmail\ContactEmailStoreRequest;
use App\Http\Requests\ContactEmail\ContactEmailUpdateRequest;
use App\Models\Contact;
use App\Models\ContactEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class ContactEmailController extends Controller
{
    protected ?string $accessEntity = 'emails';

    public function store(ContactEmailStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->emails()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_email.created'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_email.not_created'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function update(ContactEmailUpdateRequest $request, Contact $contact, ContactEmail $contactEmail): RedirectResponse
    {
        if ($contactEmail->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_email.updated'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_email.not_updated'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function destroy(Contact $contact, ContactEmail $contactEmail): RedirectResponse
    {
        $this->can('delete');

        if ($contactEmail->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_email.deleted'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_email.not_deleted'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }
}
