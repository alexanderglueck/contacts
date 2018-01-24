<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Gender;
use Intervention\Image\Facades\Image;
use Session;
use Auth;
use Illuminate\Http\Request;

class ContactController extends Controller
{

    private $validationRules = [
        'salutation' => 'present',
        'title' => 'present',
        'firstname' => 'required',
        'lastname' => 'required',
        'title_after' => 'present',
        'company' => 'present',
        'department' => 'present',
        'job' => 'present',
        'gender_id' => 'integer|exists:genders,id',
        'nickname' => 'present'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('contact.index', [
            'contacts' => Contact::sorted()->active()->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('contact.create', [
            'contact' => new Contact,
            'genders' => Gender::all(),
            'contactGroups' => ContactGroup::sorted()->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, $this->validationRules);

        $contact = new Contact();
        $contact->fill($request->all());
        $contact->created_by = Auth::id();
        $contact->updated_by = Auth::id();

        if ($contact->save()) {

            if (!is_null($request->contact_group_id) && is_array($request->contact_group_id)) {
                $contact->contactGroups()->sync($request->contact_group_id);
            } else {
                $contact->contactGroups()->sync([]);
            }

            Session::flash('alert-success', 'Kontakt wurde erstellt!');
            return redirect()->route('contacts.show', [$contact->slug]);
        } else {
            Session::flash('alert-danger', 'Kontakt konnte nicht erstellt werden!');
            return redirect()->route('contacts.create');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact)
    {
        $this->authorize('view', $contact);

        return view('contact.show', [
            'contact' => $contact
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        return view('contact.edit', [
            'createButtonText' => 'Kontakt bearbeiten',
            'contact' => $contact,
            'genders' => Gender::all(),
            'contactGroups' => ContactGroup::sorted()->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
        $this->validate($request, $this->validationRules);

        $contact->fill($request->all());
        $contact->updated_by = Auth::id();

        if (!is_null($request->contact_group_id) && is_array($request->contact_group_id)) {
            $contact->contactGroups()->sync($request->contact_group_id);
        } else {
            $contact->contactGroups()->sync([]);
        }

        if ($contact->save()) {
            Session::flash('alert-success', 'Kontakt wurde aktualisiert!');
            return redirect()->route('contacts.show', [$contact->slug]);
        } else {
            Session::flash('alert-danger', 'Kontakt konnte nicht aktualisiert werden!');
            return redirect()->route('contacts.edit', [$contact->slug]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function destroy(Contact $contact)
    {
        if ($contact->delete()) {
            Session::flash('alert-success', 'Kontakt wurde gelöscht!');
            return redirect()->route('contacts.index');
        } else {
            Session::flash('alert-danger', 'Kontakt konnte nicht gelöscht werden!');
            return redirect()->route('contacts.delete', [$contact->slug]);
        }
    }

    /**
     * Show the form for deleting the specified resource.
     *
     * @param  \App\Models\Contact $contact
     * @return \Illuminate\Http\Response
     */
    public function delete(Contact $contact)
    {
        return view('contact.delete', [
            'contact' => $contact
        ]);
    }

    public function image(Contact $contact)
    {
        return view('contact.image', [
            'contact' => $contact
        ]);
    }

    public function updateImage(Request $request, Contact $contact)
    {
        $this->validate($request, [
            'file' => 'required|mimes:jpeg,png'
        ]);

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $fileNameOriginal = $request->file('file')->storePublicly('contact_images');

            Image::make(storage_path('app/') . $fileNameOriginal)->crop(
                intval($request->image_height),
                intval($request->image_width),
                intval($request->image_x),
                intval($request->image_y)
            )->save();

            Image::make(storage_path('app/') . $fileNameOriginal)->resize(200,200)->save();

            if ($contact->image) {
                if (file_exists(storage_path('app/') . $contact->image)) {
                    unlink(storage_path('app/') . $contact->image);
                }
            }

            $contact->image = $fileNameOriginal;

            if ($contact->save()) {
                Session::flash('alert-success', 'Kontaktbild wurde aktualisiert!');
                return redirect()->route('contacts.show', $contact->slug);
            }
        } else {
            Session::flash('alert-danger', 'Kontaktbild konnte nicht aktualisiert werden!');
            return redirect()->route('contacts.image', $contact->slug);
        }


    }


}
