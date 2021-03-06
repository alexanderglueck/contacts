<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactEmail;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\ContactEmail\ContactEmailStoreRequest;
use App\Http\Requests\ContactEmail\ContactEmailUpdateRequest;

class ContactEmailController extends Controller
{
    protected $accessEntity = 'emails';

    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Contact $contact)
    {
        $this->can('view');

        return view('contact_email.index', [
            'contact' => $contact,
            'contactEmails' => $contact->emails
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Models\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Contact $contact)
    {
        $this->can('create');

        return view('contact_email.create', [
            'contact' => $contact,
            'contactEmail' => new ContactEmail()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ContactEmailStoreRequest $request
     * @param \App\Models\Contact      $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ContactEmailStoreRequest $request, Contact $contact)
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
     *
     * @param \App\Models\Contact      $contact
     * @param \App\Models\ContactEmail $contactEmail
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact, ContactEmail $contactEmail)
    {
        $this->can('view');

        return view('contact_email.show', [
            'contact' => $contact,
            'contactEmail' => $contactEmail
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Contact      $contact
     * @param \App\Models\ContactEmail $contactEmail
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact, ContactEmail $contactEmail)
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
     *
     * @param ContactEmailUpdateRequest $request
     * @param \App\Models\Contact       $contact
     * @param \App\Models\ContactEmail  $contactEmail
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ContactEmailUpdateRequest $request, Contact $contact, ContactEmail $contactEmail)
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
     *
     * @param \App\Models\Contact      $contact
     * @param \App\Models\ContactEmail $contactEmail
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Contact $contact, ContactEmail $contactEmail)
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
     *
     * @param \App\Models\Contact      $contact
     * @param \App\Models\ContactEmail $contactEmail
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Contact $contact, ContactEmail $contactEmail)
    {
        $this->can('delete');

        return view('contact_email.delete', [
            'contact' => $contact,
            'contactEmail' => $contactEmail
        ]);
    }
}
