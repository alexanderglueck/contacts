<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactNumber;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\ContactNumber\ContactNumberStoreRequest;
use App\Http\Requests\ContactNumber\ContactNumberUpdateRequest;

class ContactNumberController extends Controller
{
    protected $accessEntity = 'numbers';

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

        return view('contact_number.index', [
            'contact' => $contact,
            'contactNumbers' => $contact->numbers
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

        return view('contact_number.create', [
            'contact' => $contact,
            'contactNumber' => new ContactNumber()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ContactNumberStoreRequest $request
     * @param \App\Models\Contact       $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ContactNumberStoreRequest $request, Contact $contact)
    {
        if ($contact->numbers()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_number.created'));

            return redirect()->route('contact_numbers.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_number.not_created'));

            return redirect()->route('contact_numbers.create', [$contact->slug]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param \App\Models\Contact       $contact
     * @param \App\Models\ContactNumber $contactNumber
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact, ContactNumber $contactNumber)
    {
        $this->can('view');

        return view('contact_number.show', [
            'contact' => $contact,
            'contactNumber' => $contactNumber
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Contact       $contact
     * @param \App\Models\ContactNumber $contactNumber
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact, ContactNumber $contactNumber)
    {
        $this->can('edit');

        return view('contact_number.edit', [
            'contact' => $contact,
            'contactNumber' => $contactNumber,
            'createButtonText' => 'Nummer aktualisieren'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ContactNumberUpdateRequest $request
     * @param \App\Models\Contact        $contact
     * @param \App\Models\ContactNumber  $contactNumber
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ContactNumberUpdateRequest $request, Contact $contact, ContactNumber $contactNumber)
    {
        if ($contactNumber->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_number.updated'));

            return redirect()->route('contact_numbers.show', [$contact->slug, $contactNumber->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_number.not_updated'));

            return redirect()->route('contact_numbers.edit', [$contact->slug, $contactNumber->slug]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Contact       $contact
     * @param \App\Models\ContactNumber $contactNumber
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Contact $contact, ContactNumber $contactNumber)
    {
        $this->can('delete');

        if ($contactNumber->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_number.deleted'));

            return redirect()->route('contact_numbers.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_number.not_deleted'));

            return redirect()->route('contact_numbers.delete', [$contact->slug, $contactNumber->slug]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param \App\Models\Contact       $contact
     * @param \App\Models\ContactNumber $contactNumber
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Contact $contact, ContactNumber $contactNumber)
    {
        $this->can('delete');

        return view('contact_number.delete', [
            'contact' => $contact,
            'contactNumber' => $contactNumber
        ]);
    }
}
