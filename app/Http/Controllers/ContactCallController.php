<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class ContactCallController extends Controller
{
    protected $accessEntity = 'calls';

    private $validationRules = [
        'note' => 'nullable',
        'called_at' => 'required|date_format:d.m.Y H:i'
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

        return view('contact_call.index', [
            'contact' => $contact,
            'contactCalls' => $contact->calls
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

        return view('contact_call.create', [
            'contact' => $contact,
            'contactCall' => new ContactCall()
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

        $this->validate($request, $this->validationRules);

        $contactCall = new ContactCall();
        $contactCall->fill($request->all());
        $contactCall->contact_id = $contact->id;
        $contactCall->created_by = Auth::id();
        $contactCall->updated_by = Auth::id();

        if ($contactCall->save()) {
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
     * @param  \App\Models\Contact     $contact
     * @param  \App\Models\ContactCall $contactCall
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact, ContactCall $contactCall)
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
     * @param  \App\Models\Contact     $contact
     * @param  \App\Models\ContactCall $contactCall
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact, ContactCall $contactCall)
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
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Contact      $contact
     * @param  \App\Models\ContactCall  $contactCall
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact, ContactCall $contactCall)
    {
        $this->can('edit');

        $this->validate($request, $this->validationRules);

        $contactCall->fill($request->all());
        $contactCall->updated_by = Auth::id();

        if ($contactCall->save()) {
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
     * @param  \App\Models\Contact     $contact
     * @param  \App\Models\ContactCall $contactCall
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Contact $contact, ContactCall $contactCall)
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
     * @param  \App\Models\Contact     $contact
     * @param  \App\Models\ContactCall $contactCall
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Contact $contact, ContactCall $contactCall)
    {
        $this->can('delete');

        return view('contact_call.delete', [
            'contact' => $contact,
            'contactCall' => $contactCall
        ]);
    }
}
