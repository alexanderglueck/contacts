<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactDate;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\ContactDate\ContactDateStoreRequest;
use App\Http\Requests\ContactDate\ContactDateUpdateRequest;

class ContactDateController extends Controller
{
    protected ?string $accessEntity = 'dates';

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

        return view('contact_date.index', [
            'contact' => $contact,
            'contactDates' => $contact->dates
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

        return view('contact_date.create', [
            'contact' => $contact,
            'contactDate' => new ContactDate()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ContactDateStoreRequest $request
     * @param \App\Models\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ContactDateStoreRequest $request, Contact $contact): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->skip_year) {
            $validated['date'] .= 1900;
        }

        if ($contact->contactDates()->create($validated)) {
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
     * @param \App\Models\Contact $contact
     * @param \App\Models\ContactDate $contactDate
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact, ContactDate $contactDate): View
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
     * @param \App\Models\Contact $contact
     * @param \App\Models\ContactDate $contactDate
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact, ContactDate $contactDate): View
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
     * @param ContactDateUpdateRequest $request
     * @param \App\Models\Contact $contact
     * @param \App\Models\ContactDate $contactDate
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ContactDateUpdateRequest $request, Contact $contact, ContactDate $contactDate): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->skip_year) {
            $validated['date'] .= 1900;
        }

        if ($contactDate->update($validated)) {
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
     * @param \App\Models\Contact $contact
     * @param \App\Models\ContactDate $contactDate
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Contact $contact, ContactDate $contactDate): RedirectResponse
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
     */
    public function delete(Contact $contact, ContactDate $contactDate): View
    {
        $this->can('delete');

        return view('contact_date.delete', [
            'contact' => $contact,
            'contactDate' => $contactDate
        ]);
    }
}
