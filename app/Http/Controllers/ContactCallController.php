<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCall\ContactCallStoreRequest;
use App\Http\Requests\ContactCall\ContactCallUpdateRequest;
use App\Models\Contact;
use App\Models\ContactCall;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class ContactCallController extends Controller
{
    protected ?string $accessEntity = 'calls';

    public function store(ContactCallStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->calls()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_call.created'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_call.not_created'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function update(ContactCallUpdateRequest $request, Contact $contact, ContactCall $contactCall): RedirectResponse
    {
        if ($contactCall->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_call.updated'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_call.not_updated'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function destroy(Contact $contact, ContactCall $contactCall): RedirectResponse
    {
        $this->can('delete');

        if ($contactCall->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_call.deleted'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_call.not_deleted'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }
}
