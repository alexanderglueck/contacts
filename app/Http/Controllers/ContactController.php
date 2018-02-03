<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use App\Models\Gender;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Events\ContactCreated;
use App\Rules\ValidIBANFormat;
use Intervention\Image\Facades\Image;

class ContactController extends Controller
{
    private $validationRules = [
        'salutation' => 'required',
        'title' => 'present',
        'firstname' => 'required',
        'lastname' => 'required',
        'title_after' => 'present',
        'company' => 'present',
        'department' => 'present',
        'job' => 'present',
        'gender_id' => 'integer|exists:genders,id',
        'nickname' => 'present',
        'date_of_birth' => 'nullable|sometimes|date_format:d.m.Y',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('contact.index', [
            'contacts' => Auth::user()->contacts()->sorted()->active()->paginate(10)
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
            'contactGroups' => Auth::user()->contactGroups()->sorted()->get()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, array_merge($this->validationRules, [
            'iban' => new ValidIBANFormat
        ]));

        $contact = new Contact();
        $contact->fill($request->all());
        $contact->created_by = Auth::id();
        $contact->updated_by = Auth::id();

        if ($contact->save()) {
            if ( ! is_null($request->contact_groups) && is_array($request->contact_groups)) {
                $contact->contactGroups()->sync($request->contact_groups);
            } else {
                $contact->contactGroups()->sync([]);
            }

            event(new ContactCreated($contact));

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
     *
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
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
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact)
    {
        return view('contact.edit', [
            'createButtonText' => 'Kontakt bearbeiten',
            'contact' => $contact,
            'genders' => Gender::all(),
            'contactGroups' => Auth::user()->contactGroups()->sorted()->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Models\Contact      $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Contact $contact)
    {
        $this->validate($request, array_merge($this->validationRules, [
            'iban' => new ValidIBANFormat
        ]));

        $contact->fill($request->all());
        $contact->updated_by = Auth::id();

        if ( ! is_null($request->contact_groups) && is_array($request->contact_groups)) {
            $contact->contactGroups()->sync($request->contact_groups);
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
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
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
     *
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

            Image::make(storage_path('app/') . $fileNameOriginal)->resize(200, 200)->save();

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
