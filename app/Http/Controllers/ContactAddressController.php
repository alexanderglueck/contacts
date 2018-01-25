<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use App\Models\Contact;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Models\ContactAddress;

class ContactAddressController extends Controller
{
    private $validationRules = [
        'name' => 'required',
        'street' => 'required',
        'zip' => 'required',
        'city' => 'required',
        'state' => 'required',
        'country_id' => 'required|exists:countries,id',
    ];

    /**
     * Display a listing of the resource.
     *
     * @param  \App\Models\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function index(Contact $contact)
    {
        return view('contact_address.index', [
            'contact' => $contact,
            'contactAddresses' => $contact->addresses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \App\Models\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function create(Contact $contact)
    {
        return view('contact_address.create', [
            'contact' => $contact,
            'countries' => Country::all(),
            'contactAddress' => new ContactAddress
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Contact $contact)
    {
        if (strlen($request->latitude) > 0 && strlen($request->longitude) > 0) {
            $this->validationRules['latitude'] = 'required|numeric';
            $this->validationRules['longitude'] = 'required|numeric';
        }

        $this->validate($request, $this->validationRules);

        $contactAddress = new ContactAddress();
        $contactAddress->fill($request->all());
        $contactAddress->contact_id = $contact->id;
        $contactAddress->created_by = Auth::id();
        $contactAddress->updated_by = Auth::id();

        if ($contactAddress->save()) {
            Session::flash('alert-success', 'Adresse wurde erstellt!');

            return redirect()->route('contact_addresses.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', 'Adresse konnte nicht erstellt werden!');

            return redirect()->route('contact_addresses.create', [$contact->slug]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contact $contact
     * @param  \App\Models\ContactAddress $contactAddress
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact, ContactAddress $contactAddress)
    {
        return view('contact_address.show', [
            'contact' => $contact,
            'contactAddress' => $contactAddress
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contact $contact
     * @param  \App\Models\ContactAddress $contactAddress
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact, ContactAddress $contactAddress)
    {
        return view('contact_address.edit', [
            'createButtonText' => 'Adresse bearbeiten',
            'contact' => $contact,
            'contactAddress' => $contactAddress,
            'countries' => Country::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Contact $contact
     * @param  \App\Models\ContactAddress $contactAddress
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact, ContactAddress $contactAddress)
    {
        if (strlen($request->latitude) > 0 && strlen($request->longitude) > 0) {
            $this->validationRules['latitude'] = 'required|numeric';
            $this->validationRules['longitude'] = 'required|numeric';
        }

        $this->validate($request, $this->validationRules);

        $contactAddress->fill($request->all());
        $contactAddress->updated_by = Auth::id();

        if ($contactAddress->save()) {
            Session::flash('alert-success', 'Adresse wurde aktualisiert!');

            return redirect()->route('contact_addresses.show', [$contact->slug, $contactAddress->slug]);
        } else {
            Session::flash('alert-danger', 'Adresse konnte nicht aktualisiert werden!');

            return redirect()->route('contact_addresses.edit', [$contact->slug, $contactAddress->slug]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contact        $contact
     * @param  \App\Models\ContactAddress $contactAddress
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Contact $contact, ContactAddress $contactAddress)
    {
        if ($contactAddress->delete()) {
            Session::flash('alert-success', 'Adresse wurde gelöscht!');

            return redirect()->route('contact_addresses.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', 'Adresse konnte nicht gelöscht werden!');

            return redirect()->route('contact_addresses.delete', [$contact->slug, $contactAddress->slug]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  \App\Models\Contact $contact
     * @param  \App\Models\ContactAddress $contactAddress
     * @return \Illuminate\Http\Response
     */
    public function delete(Contact $contact, ContactAddress $contactAddress)
    {
        return view('contact_address.delete', [
            'contact' => $contact,
            'contactAddress' => $contactAddress
        ]);
    }
}
