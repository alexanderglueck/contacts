<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactAddress\ContactAddressStoreRequest;
use App\Http\Requests\ContactAddress\ContactAddressUpdateRequest;
use App\Models\Contact;
use App\Models\ContactAddress;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class ContactAddressController extends Controller
{
    protected ?string $accessEntity = 'addresses';

    public function store(ContactAddressStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->addresses()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_address.created'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_address.not_created'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function update(ContactAddressUpdateRequest $request, Contact $contact, ContactAddress $contactAddress): RedirectResponse
    {
        if ($contactAddress->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_address.updated'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_address.not_updated'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function destroy(Contact $contact, ContactAddress $contactAddress): RedirectResponse
    {
        $this->can('delete');

        if ($contactAddress->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_address.deleted'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_address.not_deleted'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }
}
