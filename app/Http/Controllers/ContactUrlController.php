<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactUrl\ContactUrlStoreRequest;
use App\Http\Requests\ContactUrl\ContactUrlUpdateRequest;
use App\Models\Contact;
use App\Models\ContactUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class ContactUrlController extends Controller
{
    protected ?string $accessEntity = 'urls';

    public function store(ContactUrlStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->urls()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_url.created'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_url.not_created'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function update(ContactUrlUpdateRequest $request, Contact $contact, ContactUrl $contactUrl): RedirectResponse
    {
        if ($contactUrl->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_url.updated'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_url.not_updated'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function destroy(Contact $contact, ContactUrl $contactUrl): RedirectResponse
    {
        $this->can('delete');

        if ($contactUrl->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_url.deleted'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_url.not_deleted'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }
}
