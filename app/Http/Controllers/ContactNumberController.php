<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactNumber\ContactNumberStoreRequest;
use App\Http\Requests\ContactNumber\ContactNumberUpdateRequest;
use App\Models\Contact;
use App\Models\ContactNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class ContactNumberController extends Controller
{
    protected ?string $accessEntity = 'numbers';

    public function store(ContactNumberStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->numbers()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_number.created'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_number.not_created'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function update(ContactNumberUpdateRequest $request, Contact $contact, ContactNumber $contactNumber): RedirectResponse
    {
        if ($contactNumber->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_number.updated'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_number.not_updated'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function destroy(Contact $contact, ContactNumber $contactNumber): RedirectResponse
    {
        $this->can('delete');

        if ($contactNumber->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_number.deleted'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_number.not_deleted'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }
}
