<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ContactDateController extends Controller
{
    protected $accessEntity = 'dates';

    private $validationRules = [
        'name' => 'required',
        'date' => 'required|date_format:d.m.',
        'skip_year' => 'boolean'
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
        $this->can('view');

        return view('contact_date.index', [
            'contact' => $contact,
            'contactDates' => $contact->dates
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
        $this->can('create');

        return view('contact_date.create', [
            'contact' => $contact,
            'contactDate' => new ContactDate()
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
        $this->can('create');

        $requestData = $request->all();

        if ($request->skip_year) {
            $requestData['date'] .= '1900';
        } else {
            $this->validationRules['date'] .= 'Y';
        }

        $this->validate($request, $this->validationRules);

        $contactDate = new ContactDate();
        $contactDate->fill($requestData);
        $contactDate->contact_id = $contact->id;
        $contactDate->created_by = Auth::id();
        $contactDate->updated_by = Auth::id();

        if ($contactDate->save()) {
            Session::flash('alert-success', trans('flash_message.contact_date.created'));

            return redirect()->route('contact_dates.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_date.not_created'));

            return redirect()->route('contact_dates.create', [$contact->slug]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contact     $contact
     * @param  \App\Models\ContactDate $contactDate
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact, ContactDate $contactDate)
    {
        $this->can('view');

        return view('contact_date.show', [
            'contact' => $contact,
            'contactDate' => $contactDate
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contact     $contact
     * @param  \App\Models\ContactDate $contactDate
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact, ContactDate $contactDate)
    {
        $this->can('edit');

        return view('contact_date.edit', [
            'contact' => $contact,
            'contactDate' => $contactDate,
            'createButtonText' => 'Datum bearbeiten'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Contact      $contact
     * @param  \App\Models\ContactDate  $contactDate
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact, ContactDate $contactDate)
    {
        $this->can('edit');

        $requestData = $request->all();

        if ($request->skip_year) {
            $requestData['date'] .= '1900';
        } else {
            $this->validationRules['date'] .= 'Y';
        }

        if ( ! isset($request->skip_year)) {
            $request->skip_year = 0;
        }

        $this->validate($request, $this->validationRules);

        $contactDate->fill($requestData);
        $contactDate->updated_by = Auth::id();

        if ($contactDate->save()) {
            Session::flash('alert-success', trans('flash_message.contact_date.updated'));

            return redirect()->route('contact_dates.show', [$contact->slug, $contactDate->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_date.not_updated'));

            return redirect()->route('contact_dates.edit', [$contact->slug, $contactDate->slug]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contact     $contact
     * @param  \App\Models\ContactDate $contactDate
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Contact $contact, ContactDate $contactDate)
    {
        $this->can('delete');

        if ($contactDate->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_date.deleted'));

            return redirect()->route('contact_dates.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_date.not_deleted'));

            return redirect()->route('contact_dates.delete', [$contact->slug, $contactDate->slug]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  \App\Models\Contact     $contact
     * @param  \App\Models\ContactDate $contactDate
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Contact $contact, ContactDate $contactDate)
    {
        $this->can('delete');

        return view('contact_date.delete', [
            'contact' => $contact,
            'contactDate' => $contactDate
        ]);
    }
}
