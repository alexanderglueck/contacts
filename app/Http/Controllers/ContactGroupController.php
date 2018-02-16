<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use Validator;
use App\Models\ContactGroup;
use Illuminate\Http\Request;

class ContactGroupController extends Controller
{
    private $validationRules = [
        'name' => 'required',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('contact_group.index', [
            'contactGroups' => Auth::user()->contactGroups()->sorted()->paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contact_group.create', [
            'contactGroups' => Auth::user()->contactGroups()->sorted()->get(),
            'contactGroup' => new ContactGroup()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validationRules);
        $validator->sometimes('parent_id', 'exists:contact_groups,id', function ($input) {
            return strlen($input->parent_id) > 0;
        });

        $validator->validate();

        $contact = new ContactGroup();
        $contact->fill($request->all());
        $contact->created_by = Auth::id();
        $contact->updated_by = Auth::id();

        if ($contact->save()) {
            Session::flash('alert-success', 'Kontaktgruppe wurde erstellt!');

            return redirect()->route('contact_groups.index');
        } else {
            Session::flash('alert-danger', 'Kontaktgruppe konnte nicht erstellt werden!');

            return redirect()->route('contact_groups.create');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ContactGroup $contactGroup
     *
     * @return \Illuminate\Http\Response
     */
    public function show(ContactGroup $contactGroup)
    {
        return view('contact_group.show', [
            'contactGroup' => $contactGroup,
            'contacts' => $contactGroup->contacts()->paginate(10)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\ContactGroup $contactGroup
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(ContactGroup $contactGroup)
    {
        return view('contact_group.edit', [
            'contactGroup' => $contactGroup,
            'contactGroups' => ContactGroup::sorted()->get(),
            'createButtonText' => 'Kontaktgruppe aktualisieren'
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\ContactGroup $contactGroup
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, ContactGroup $contactGroup)
    {
        $validator = Validator::make($request->all(), $this->validationRules);
        $validator->sometimes('parent_id', 'exists:contact_groups,id|not_in:' . $contactGroup->id, function ($input) {
            return strlen($input->parent_id) > 0;
        });

        $validator->validate();

        $contactGroup->fill($request->all());
        $contactGroup->updated_by = Auth::id();

        if ($contactGroup->save()) {
            Session::flash('alert-success', 'Kontaktgruppe wurde aktualisiert!');

            return redirect()->route('contact_groups.show', [$contactGroup->slug]);
        } else {
            Session::flash('alert-danger', 'Kontaktgruppe konnte nicht aktualisiert werden!');

            return redirect()->route('contact_groups.edit', [$contactGroup->slug]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ContactGroup $contactGroup
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(ContactGroup $contactGroup)
    {
        if ($contactGroup->delete()) {
            Session::flash('alert-success', 'Kontaktgruppe wurde gelöscht!');

            return redirect()->route('contact_groups.index');
        } else {
            Session::flash('alert-danger', 'Kontaktgruppe konnte nicht gelöscht werden!');

            return redirect()->route('contact_groups.delete', $contactGroup->slug);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  \App\Models\ContactGroup $contactGroup
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(ContactGroup $contactGroup)
    {
        return view('contact_group.delete', [
            'contactGroup' => $contactGroup
        ]);
    }
}
