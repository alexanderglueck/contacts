<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactEmail\ContactEmailStoreRequest;
use App\Http\Requests\ContactEmail\ContactEmailUpdateRequest;
use App\Models\Contact;
use App\Models\ContactEmail;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class ContactEmailController extends Controller
{
    protected ?string $accessEntity = 'emails';

    /**
     * Display a listing of the resource.
     */
    public function index(Contact $contact): View
    {
        $this->can('view');

        return view('contact_email.index', [
            'contact' => $contact,
            'contactEmails' => $contact->emails
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Contact $contact): View
    {
        $this->can('create');

        return view('contact_email.create', [
            'contact' => $contact,
            'contactEmail' => new ContactEmail()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactEmailStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->emails()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_email.created'));

            return redirect()->route('contact_emails.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_email.not_created'));

            return redirect()->route('contact_emails.create', [$contact->slug]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact, ContactEmail $contactEmail): View
    {
        $this->can('view');

        return view('contact_email.show', [
            'contact' => $contact,
            'contactEmail' => $contactEmail
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact, ContactEmail $contactEmail): View
    {
        $this->can('edit');

        return view('contact_email.edit', [
            'contact' => $contact,
            'contactEmail' => $contactEmail,
            'createButtonText' => 'E-Mail Adresse aktualisieren'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContactEmailUpdateRequest $request, Contact $contact, ContactEmail $contactEmail): RedirectResponse
    {
        if ($contactEmail->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_email.updated'));

            return redirect()->route('contact_emails.show', [$contact->slug, $contactEmail->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_email.not_updated'));

            return redirect()->route('contact_emails.edit', [$contact->slug, $contactEmail->slug]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact, ContactEmail $contactEmail): RedirectResponse
    {
        $this->can('delete');

        if ($contactEmail->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_email.deleted'));

            return redirect()->route('contact_emails.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_email.not_deleted'));

            return redirect()->route('contact_emails.delete', [$contact->slug, $contactEmail->slug]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Contact $contact, ContactEmail $contactEmail): View
    {
        $this->can('delete');

        return view('contact_email.delete', [
            'contact' => $contact,
            'contactEmail' => $contactEmail
        ]);
    }
}
