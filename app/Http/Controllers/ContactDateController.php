<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactDate\ContactDateStoreRequest;
use App\Http\Requests\ContactDate\ContactDateUpdateRequest;
use App\Models\Contact;
use App\Models\ContactDate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class ContactDateController extends Controller
{
    protected ?string $accessEntity = 'dates';

    public function store(ContactDateStoreRequest $request, Contact $contact): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->skip_year) {
            $validated['date'] .= 1900;
        }

        if ($contact->contactDates()->create($validated)) {
            Session::flash('alert-success', trans('flash_message.contact_date.created'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_date.not_created'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function update(ContactDateUpdateRequest $request, Contact $contact, ContactDate $contactDate): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->skip_year) {
            $validated['date'] .= 1900;
        }

        if ($contactDate->update($validated)) {
            Session::flash('alert-success', trans('flash_message.contact_date.updated'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_date.not_updated'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }

    public function destroy(Contact $contact, ContactDate $contactDate): RedirectResponse
    {
        $this->can('delete');

        if ($contactDate->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_date.deleted'));
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_date.not_deleted'));
        }

        return redirect()->route('contacts.show', $contact->ulid);
    }
}
