<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use App\Models\Gender;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Country;
use Illuminate\Http\Request;
use App\Rules\ValidIBANFormat;
use Intervention\Image\Facades\Image;

class ContactController extends Controller
{
    protected $accessEntity = 'contacts';

    private $validationRules = [
        'salutation' => 'required',
        'title' => 'present',
        'firstname' => 'required',
        'lastname' => 'required',
        'title_after' => 'present',
        'company' => 'present',
        'department' => 'present',
        'job' => 'present',
        'gender_id' => 'integer|exists:system.genders,id',
        'nickname' => 'present',
        'date_of_birth' => 'nullable|sometimes|date_format:d.m.Y',
        'died_at' => 'nullable|sometimes|date_format:d.m.Y',
        'nationality_id' => 'nullable|sometimes|exists:system.countries,id',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->can('view');

        return view('contact.index', [
            'contacts' => Contact::sorted()->active()->paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->can('create');

        return view('contact.create', [
            'contact' => new Contact,
            'genders' => Gender::all(),
            'contactGroups' => Auth::user()->contactGroups()->sorted()->get(),
            'countries' => Country::all()
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
        $this->can('create');

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

            Session::flash('alert-success', trans('flash_message.contact.created'));

            return redirect()->route('contacts.show', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact.not_created'));

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
        $this->can('view');

        return view('contact.show', [
            'contact' => $contact,
            'comments' => $contact->getThreadedComments(),
            'newComment' => new Comment()
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
        $this->can('edit');

        return view('contact.edit', [
            'createButtonText' => 'Kontakt bearbeiten',
            'contact' => $contact,
            'genders' => Gender::all(),
            'contactGroups' => Auth::user()->contactGroups()->sorted()->get(),
            'countries' => Country::all()
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
        $this->can('edit');

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
            flashSuccess(trans('flash_message.contact.updated'));

            return redirect()->route('contacts.show', [$contact->slug]);
        } else {
            flashError(trans('flash_message.contact.not_updated'));

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
        $this->can('delete');

        if ($contact->image) {
            if (file_exists(storage_path('app/') . $contact->image)) {
                unlink(storage_path('app/') . $contact->image);
            }
        }

        if ($contact->delete()) {
            Session::flash('alert-success', trans('flash_message.contact.deleted'));

            return redirect()->route('contacts.index');
        } else {
            Session::flash('alert-danger', trans('flash_message.contact.not_deleted'));

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
        $this->can('delete');

        return view('contact.delete', [
            'contact' => $contact
        ]);
    }

    public function image(Contact $contact)
    {
        $this->can('edit');

        return view('contact.image', [
            'contact' => $contact
        ]);
    }

    public function updateImage(Request $request, Contact $contact)
    {
        $this->can('edit');

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
                Session::flash('alert-success', trans('flash_message.contact.updated'));

                return redirect()->route('contacts.show', $contact->slug);
            }
        }

        Session::flash('alert-danger', trans('flash_message.contact.not_updated'));

        return redirect()->route('contacts.image', $contact->slug);
    }
}
