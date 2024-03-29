<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactUrl;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\ContactUrl\ContactUrlStoreRequest;
use App\Http\Requests\ContactUrl\ContactUrlUpdateRequest;

class ContactUrlController extends Controller
{
    protected ?string $accessEntity = 'urls';

    /**
     * Display a listing of the resource.
     */
    public function index(Contact $contact): View
    {
        $this->can('view');

        return view('contact_url.index', [
            'contact' => $contact,
            'contactUrls' => $contact->urls
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Contact $contact): View
    {
        $this->can('create');

        return view('contact_url.create', [
            'contact' => $contact,
            'contactUrl' => new ContactUrl()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ContactUrlStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->urls()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_url.created'));

            return redirect()->route('contact_urls.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_url.not_created'));

            return redirect()->route('contact_urls.create', [$contact->slug]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact, ContactUrl $contactUrl): View
    {
        $this->can('view');

        return view('contact_url.show', [
            'contact' => $contact,
            'contactUrl' => $contactUrl
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Contact $contact, ContactUrl $contactUrl): View
    {
        $this->can('edit');

        return view('contact_url.edit', [
            'contact' => $contact,
            'contactUrl' => $contactUrl,
            'createButtonText' => 'Website aktualisieren'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ContactUrlUpdateRequest $request, Contact $contact, ContactUrl $contactUrl): RedirectResponse
    {
        if ($contactUrl->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_url.updated'));

            return redirect()->route('contact_urls.show', [$contact->slug, $contactUrl->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_url.not_updated'));

            return redirect()->route('contact_urls.edit', [$contact->slug, $contactUrl->slug]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact, ContactUrl $contactUrl): RedirectResponse
    {
        $this->can('delete');

        if ($contactUrl->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_url.deleted'));

            return redirect()->route('contact_urls.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_url.not_deleted'));

            return redirect()->route('contact_urls.delete', [$contact->slug, $contactUrl->slug]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(Contact $contact, ContactUrl $contactUrl): View
    {
        $this->can('delete');

        return view('contact_url.delete', [
            'contact' => $contact,
            'contactUrl' => $contactUrl
        ]);
    }
}
