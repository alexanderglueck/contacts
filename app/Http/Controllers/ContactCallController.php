<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactCall;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\ContactCall\ContactCallStoreRequest;
use App\Http\Requests\ContactCall\ContactCallUpdateRequest;

class ContactCallController extends Controller
{
    protected ?string $accessEntity = 'calls';

    /**
     * Display a listing of the resource.
     *
     * @param \App\Models\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Contact $contact): View
    {
        $this->can('view');

        return view('contact_call.index', [
            'contact' => $contact,
            'contactCalls' => $contact->calls
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param \App\Models\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Contact $contact): View
    {
        $this->can('create');

        return view('contact_call.create', [
            'contact' => $contact,
            'contactCall' => new ContactCall()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ContactCallStoreRequest $request
     * @param \App\Models\Contact     $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ContactCallStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->calls()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_call.created'));

            return redirect()->route('contact_calls.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_call.not_created'));

            return redirect()->route('contact_calls.create', [$contact->slug]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Contact     $contact
     * @param \App\Models\ContactCall $contactCall
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact, ContactCall $contactCall): View
    {
        $this->can('view');

        return view('contact_call.show', [
            'contact' => $contact,
            'contactCall' => $contactCall
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Contact     $contact
     * @param \App\Models\ContactCall $contactCall
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact, ContactCall $contactCall): View
    {
        $this->can('edit');

        return view('contact_call.edit', [
            'contact' => $contact,
            'contactCall' => $contactCall,
            'createButtonText' => 'Website aktualisieren'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ContactCallUpdateRequest $request
     * @param \App\Models\Contact      $contact
     * @param \App\Models\ContactCall  $contactCall
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ContactCallUpdateRequest $request, Contact $contact, ContactCall $contactCall): RedirectResponse
    {
        if ($contactCall->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_call.updated'));

            return redirect()->route('contact_calls.show', [$contact->slug, $contactCall->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_call.not_updated'));

            return redirect()->route('contact_calls.edit', [$contact->slug, $contactCall->slug]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Contact     $contact
     * @param \App\Models\ContactCall $contactCall
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Contact $contact, ContactCall $contactCall): RedirectResponse
    {
        $this->can('delete');

        if ($contactCall->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_call.deleted'));

            return redirect()->route('contact_calls.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_call.not_deleted'));

            return redirect()->route('contact_calls.delete', [$contact->slug, $contactCall->slug]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param \App\Models\Contact     $contact
     * @param \App\Models\ContactCall $contactCall
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Contact $contact, ContactCall $contactCall): View
    {
        $this->can('delete');

        return view('contact_call.delete', [
            'contact' => $contact,
            'contactCall' => $contactCall
        ]);
    }
}
