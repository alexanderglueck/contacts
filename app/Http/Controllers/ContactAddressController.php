<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Country;
use App\Models\ContactAddress;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\ContactAddress\ContactAddressStoreRequest;
use App\Http\Requests\ContactAddress\ContactAddressUpdateRequest;

class ContactAddressController extends Controller
{
    protected ?string $accessEntity = 'addresses';

    /**
     * Display a listing of the resource.
     */
    public function index(Contact $contact): View
    {
        $this->can('view');

        return view('contact_address.index', [
            'contact' => $contact,
            'contactAddresses' => $contact->addresses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Contact $contact): View
    {
        $this->can('create');

        return view('contact_address.create', [
            'contact' => $contact,
            'countries' => Country::all(),
            'contactAddress' => new ContactAddress
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactAddressStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->addresses()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_address.created'));

            return redirect()->route('contact_addresses.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_address.not_created'));

            return redirect()->route('contact_addresses.create', [$contact->slug]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact, ContactAddress $contactAddress): View
    {
        $this->can('view');

        return view('contact_address.show', [
            'contact' => $contact,
            'contactAddress' => $contactAddress
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact, ContactAddress $contactAddress): View
    {
        $this->can('edit');

        return view('contact_address.edit', [
            'createButtonText' => 'Adresse bearbeiten',
            'contact' => $contact,
            'contactAddress' => $contactAddress,
            'countries' => Country::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContactAddressUpdateRequest $request, Contact $contact, ContactAddress $contactAddress): RedirectResponse
    {
        if ($contactAddress->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_address.updated'));

            return redirect()->route('contact_addresses.show', [$contact->slug, $contactAddress->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_address.not_updated'));

            return redirect()->route('contact_addresses.edit', [$contact->slug, $contactAddress->slug]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact, ContactAddress $contactAddress): RedirectResponse
    {
        $this->can('delete');

        if ($contactAddress->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_address.deleted'));

            return redirect()->route('contact_addresses.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_address.not_deleted'));

            return redirect()->route('contact_addresses.delete', [$contact->slug, $contactAddress->slug]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Contact $contact, ContactAddress $contactAddress): View
    {
        $this->can('delete');

        return view('contact_address.delete', [
            'contact' => $contact,
            'contactAddress' => $contactAddress
        ]);
    }
}
