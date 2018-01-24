<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactUrl;
use Auth;
use Session;
use Illuminate\Http\Request;

class ContactUrlController extends Controller
{
    private $validationRules = [
        'name' => 'required',
        'url' => 'required|url'
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
        return view('contact_url.index', [
            'contact' => $contact,
            'contactUrls' => $contact->urls
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
        return view('contact_url.create', [
            'contact' => $contact,
            'contactUrl' => new ContactUrl()
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

        $contactUrl = new ContactUrl();
        $contactUrl->fill($request->all());
        $contactUrl->contact_id = $contact->id;
        $contactUrl->created_by = Auth::id();
        $contactUrl->updated_by = Auth::id();

        if ($contactUrl->save()) {
            Session::flash('alert-success', 'Website wurde erstellt!');

            return redirect()->route('contact_urls.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', 'Website konnte nicht erstellt werden!');

            return redirect()->route('contact_urls.create', [$contact->slug]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contact    $contact
     * @param  \App\Models\ContactUrl $contactUrl
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact, ContactUrl $contactUrl)
    {
        return view('contact_url.show', [
            'contact' => $contact,
            'contactUrl' => $contactUrl
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contact    $contact
     * @param  \App\Models\ContactUrl $contactUrl
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact, ContactUrl $contactUrl)
    {
        return view('contact_url.edit', [
            'contact' => $contact,
            'contactUrl' => $contactUrl,
            'createButtonText' => 'Website aktualisieren'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Contact      $contact
     * @param  \App\Models\ContactUrl   $contactUrl
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact, ContactUrl $contactUrl)
    {
        $this->validate($request, $this->validationRules);

        $contactUrl->fill($request->all());
        $contactUrl->updated_by = Auth::id();

        if ($contactUrl->save()) {
            Session::flash('alert-success', 'Website wurde aktualisiert!');

            return redirect()->route('contact_urls.show', [$contact->slug, $contactUrl->slug]);
        } else {
            Session::flash('alert-danger', 'Website konnte nicht aktualisiert werden!');

            return redirect()->route('contact_urls.edit', [$contact->slug, $contactUrl->slug]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contact    $contact
     * @param  \App\Models\ContactUrl $contactUrl
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Contact $contact, ContactUrl $contactUrl)
    {
        if ($contactUrl->delete()) {
            Session::flash('alert-success', 'Website wurde gelöscht!');

            return redirect()->route('contact_urls.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', 'Website konnte nicht gelöscht werden!');

            return redirect()->route('contact_urls.delete', [$contact->slug, $contactUrl->slug]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  \App\Models\Contact    $contact
     * @param  \App\Models\ContactUrl $contactUrl
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Contact $contact, ContactUrl $contactUrl)
    {
        return view('contact_url.delete', [
            'contact' => $contact,
            'contactUrl' => $contactUrl
        ]);
    }
}
