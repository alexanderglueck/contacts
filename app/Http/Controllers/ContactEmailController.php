<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use App\Models\Contact;
use App\Models\ContactEmail;
use Illuminate\Http\Request;

class ContactEmailController extends Controller
{
    private $validationRules = [
        'name' => 'required',
        'email' => 'required|email'
    ];

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Contact $contact)
    {
        return view('contact_email.index', [
            'contact' => $contact,
            'contactEmails' => $contact->emails
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Contact $contact)
    {
        return view('contact_email.create', [
            'contact' => $contact,
            'contactEmail' => new ContactEmail()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Contact      $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Contact $contact)
    {
        $this->validate($request, $this->validationRules);

        $contactEmail = new ContactEmail();
        $contactEmail->fill($request->all());
        $contactEmail->contact_id = $contact->id;
        $contactEmail->created_by = Auth::id();
        $contactEmail->updated_by = Auth::id();

        if ($contactEmail->save()) {
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
     * @param  \App\Models\Contact      $contact
     * @param  \App\Models\ContactEmail $contactEmail
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact, ContactEmail $contactEmail)
    {
        return view('contact_email.show', [
            'contact' => $contact,
            'contactEmail' => $contactEmail
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contact      $contact
     * @param  \App\Models\ContactEmail $contactEmail
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact, ContactEmail $contactEmail)
    {
        return view('contact_email.edit', [
            'contact' => $contact,
            'contactEmail' => $contactEmail,
            'createButtonText' => 'E-Mail Adresse aktualisieren'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Contact      $contact
     * @param  \App\Models\ContactEmail $contactEmail
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact, ContactEmail $contactEmail)
    {
        $this->validate($request, $this->validationRules);

        $contactEmail->fill($request->all());
        $contactEmail->updated_by = Auth::id();

        if ($contactEmail->save()) {
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
     * @param  \App\Models\Contact      $contact
     * @param  \App\Models\ContactEmail $contactEmail
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Contact $contact, ContactEmail $contactEmail)
    {
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
     * @param  \App\Models\Contact      $contact
     * @param  \App\Models\ContactEmail $contactEmail
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Contact $contact, ContactEmail $contactEmail)
    {
        return view('contact_email.delete', [
            'contact' => $contact,
            'contactEmail' => $contactEmail
        ]);
    }
}
