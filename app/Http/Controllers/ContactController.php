<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use App\Models\Comment;
use App\Models\Contact;
use App\Models\Country;
use App\Models\ContactGroup;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Contact\ContactStoreRequest;
use App\Http\Requests\Contact\ContactUpdateRequest;

class ContactController extends Controller
{
    protected ?string $accessEntity = 'contacts';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(): View
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
    public function create(): View
    {
        $this->can('create');

        return view('contact.create', [
            'contact' => new Contact,
            'genders' => Gender::all(),
            'contactGroups' => ContactGroup::sorted()->get(),
            'countries' => Country::all()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ContactStoreRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function store(ContactStoreRequest $request): RedirectResponse
    {
        $contact = new Contact();
        $contact->fill($request->all());

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
     * @param \App\Models\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Contact $contact): View
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
     * @param \App\Models\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Contact $contact): View
    {
        $this->can('edit');

        return view('contact.edit', [
            'createButtonText' => 'Kontakt bearbeiten',
            'contact' => $contact,
            'genders' => Gender::all(),
            'contactGroups' => ContactGroup::sorted()->get(),
            'countries' => Country::all()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ContactUpdateRequest $request
     * @param \App\Models\Contact  $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function update(ContactUpdateRequest $request, Contact $contact): RedirectResponse
    {
        if ( ! is_null($request->contact_groups) && is_array($request->contact_groups)) {
            $contact->contactGroups()->sync($request->contact_groups);
        } else {
            $contact->contactGroups()->sync([]);
        }

        if ($contact->update($request->all())) {
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
     * @param \App\Models\Contact $contact
     *
     * @return \Illuminate\Http\Response
     * @throws \Exception
     */
    public function destroy(Contact $contact): RedirectResponse
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
     * @param \App\Models\Contact $contact
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(Contact $contact): View
    {
        $this->can('delete');

        return view('contact.delete', [
            'contact' => $contact
        ]);
    }

    public function image(Contact $contact): View
    {
        $this->can('edit');

        return view('contact.image', [
            'contact' => $contact
        ]);
    }

    public function updateImage(Request $request, Contact $contact): RedirectResponse
    {
        $this->can('edit');

        $this->validate($request, [
            'file' => 'required|mimes:jpeg,png'
        ]);

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $fileNameOriginal = $request->file('file')->storePublicly('public/contact_images');

            Image::make(storage_path('app/') . $fileNameOriginal)->crop(
                intval($request->image_height),
                intval($request->image_width),
                intval($request->image_x),
                intval($request->image_y)
            )->save();

            Image::make(storage_path('app/') . $fileNameOriginal)->resize(200, 200)->save();

            if ($contact->image) {
                if (file_exists(storage_path('app/public/') . $contact->image)) {
                    unlink(storage_path('app/public/') . $contact->image);
                }
            }

            $contact->image = str_replace('public/', '', $fileNameOriginal);

            if ($contact->save()) {
                Session::flash('alert-success', trans('flash_message.contact.updated'));

                return redirect()->route('contacts.show', $contact->slug);
            }
        }

        Session::flash('alert-danger', trans('flash_message.contact.not_updated'));

        return redirect()->route('contacts.image', $contact->slug);
    }
}
