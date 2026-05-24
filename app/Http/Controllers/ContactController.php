<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Country;
use App\Models\Gender;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Facades\Image;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\Contact\ContactStoreRequest;
use App\Http\Requests\Contact\ContactUpdateRequest;

class ContactController extends Controller
{
    protected ?string $accessEntity = 'contacts';

    public function index(): Response
    {
        $this->can('view');

        return Inertia::render('Contacts/Index', [
            'contacts' => Contact::sorted()->active()->paginate(10)->through(fn ($contact) => [
                'id' => $contact->id,
                'ulid' => $contact->ulid,
                'fullname' => $contact->fullname,
            ]),
            'canCreate' => Auth::user()->checkPermissionTo('create contacts'),
        ]);
    }

    public function create(): Response
    {
        $this->can('create');

        return Inertia::render('Contacts/Create', [
            'genders' => Gender::all(['id', 'gender']),
            'contactGroups' => ContactGroup::sorted()->get(['id', 'name']),
            'countries' => Country::all(['id', 'country']),
        ]);
    }

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

            return redirect()->route('contacts.show', [$contact->ulid]);
        }

        Session::flash('alert-danger', trans('flash_message.contact.not_created'));

        return redirect()->route('contacts.create');
    }

    public function show(Contact $contact): Response
    {
        $this->can('view');

        $contact->loadCount([
            'addresses',
            'numbers',
            'emails',
            'urls',
            'dates',
            'notes',
            'calls',
            'giftIdeas as gift_ideas_count',
        ]);
        $contact->load(['gender:id,gender', 'country:id,country']);

        $user = Auth::user();

        return Inertia::render('Contacts/Show', [
            'contact' => [
                'id' => $contact->id,
                'ulid' => $contact->ulid,
                'fullname' => $contact->fullname,
                'salutation' => $contact->salutation,
                'company' => $contact->company,
                'job' => $contact->job,
                'department' => $contact->department,
                'nickname' => $contact->nickname,
                'custom_id' => $contact->custom_id,
                'iban' => $contact->iban,
                'note' => $contact->note,
                'first_met' => $contact->first_met,
                'image' => $contact->image,
                'formatted_date_of_birth' => $contact->formatted_date_of_birth,
                'gender' => $contact->gender ? ['gender' => $contact->gender->gender] : null,
                'nationality' => $contact->country ? ['country' => $contact->country->country] : null,
                'addresses_count' => $contact->addresses_count,
                'numbers_count' => $contact->numbers_count,
                'emails_count' => $contact->emails_count,
                'urls_count' => $contact->urls_count,
                'dates_count' => $contact->dates_count,
                'notes_count' => $contact->notes_count,
                'calls_count' => $contact->calls_count,
                'gift_ideas_count' => $contact->gift_ideas_count,
            ],
            'can' => [
                'edit' => $user->checkPermissionTo('edit contacts'),
                'delete' => $user->checkPermissionTo('delete contacts'),
                'view_addresses' => $user->checkPermissionTo('view addresses'),
                'view_numbers' => $user->checkPermissionTo('view numbers'),
                'view_emails' => $user->checkPermissionTo('view emails'),
                'view_urls' => $user->checkPermissionTo('view urls'),
                'view_dates' => $user->checkPermissionTo('view dates'),
                'view_notes' => $user->checkPermissionTo('view notes'),
                'view_calls' => $user->checkPermissionTo('view calls'),
                'view_gift_ideas' => $user->checkPermissionTo('view giftIdeas'),
            ],
        ]);
    }

    public function edit(Contact $contact): Response
    {
        $this->can('edit');

        return Inertia::render('Contacts/Edit', [
            'contact' => [
                'id' => $contact->id,
                'ulid' => $contact->ulid,
                'fullname' => $contact->fullname,
                'salutation' => $contact->salutation,
                'title' => $contact->title,
                'title_after' => $contact->title_after,
                'firstname' => $contact->firstname,
                'lastname' => $contact->lastname,
                'nickname' => $contact->nickname,
                'formatted_date_of_birth' => $contact->formatted_date_of_birth,
                'iban' => $contact->iban,
                'company' => $contact->company,
                'vatin' => $contact->vatin,
                'department' => $contact->department,
                'job' => $contact->job,
                'gender_id' => $contact->gender_id,
                'custom_id' => $contact->custom_id,
                'contact_groups' => $contact->contactGroups->pluck('id')->all(),
                'active' => $contact->active,
                'first_met' => $contact->first_met,
                'note' => $contact->note,
                'formatted_died_at' => $contact->formatted_died_at,
                'died_from' => $contact->died_from,
                'nationality_id' => $contact->nationality_id,
            ],
            'genders' => Gender::all(['id', 'gender']),
            'contactGroups' => ContactGroup::sorted()->get(['id', 'name']),
            'countries' => Country::all(['id', 'country']),
        ]);
    }

    public function update(ContactUpdateRequest $request, Contact $contact): RedirectResponse
    {
        if ( ! is_null($request->contact_groups) && is_array($request->contact_groups)) {
            $contact->contactGroups()->sync($request->contact_groups);
        } else {
            $contact->contactGroups()->sync([]);
        }

        if ($contact->update($request->all())) {
            flashSuccess(trans('flash_message.contact.updated'));

            return redirect()->route('contacts.show', [$contact->ulid]);
        }

        flashError(trans('flash_message.contact.not_updated'));

        return redirect()->route('contacts.edit', [$contact->ulid]);
    }

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
        }

        Session::flash('alert-danger', trans('flash_message.contact.not_deleted'));

        return redirect()->route('contacts.delete', [$contact->ulid]);
    }

    public function delete(Contact $contact): Response
    {
        $this->can('delete');

        return Inertia::render('Contacts/Delete', [
            'contact' => [
                'ulid' => $contact->ulid,
                'fullname' => $contact->fullname,
            ],
        ]);
    }

    public function image(Contact $contact): Response
    {
        $this->can('edit');

        return Inertia::render('Contacts/Image', [
            'contact' => [
                'ulid' => $contact->ulid,
                'fullname' => $contact->fullname,
                'image' => $contact->image,
            ],
        ]);
    }

    public function updateImage(Request $request, Contact $contact): RedirectResponse
    {
        $this->can('edit');

        $this->validate($request, [
            'file' => 'required|mimes:jpeg,png',
        ]);

        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $fileNameOriginal = $request->file('file')->storePublicly('public/contact_images');

            if ($request->image_height && $request->image_width) {
                Image::make(storage_path('app/') . $fileNameOriginal)->crop(
                    intval($request->image_height),
                    intval($request->image_width),
                    intval($request->image_x),
                    intval($request->image_y)
                )->save();
            }

            Image::make(storage_path('app/') . $fileNameOriginal)->resize(200, 200)->save();

            if ($contact->image) {
                if (file_exists(storage_path('app/public/') . $contact->image)) {
                    unlink(storage_path('app/public/') . $contact->image);
                }
            }

            $contact->image = str_replace('public/', '', $fileNameOriginal);

            if ($contact->save()) {
                Session::flash('alert-success', trans('flash_message.contact.updated'));

                return redirect()->route('contacts.show', $contact->ulid);
            }
        }

        Session::flash('alert-danger', trans('flash_message.contact.not_updated'));

        return redirect()->route('contacts.image', $contact->ulid);
    }
}
