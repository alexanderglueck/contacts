<?php

namespace App\Http\Controllers;

use App\Models\ContactGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Requests\ContactGroup\ContactGroupStoreRequest;
use App\Http\Requests\ContactGroup\ContactGroupUpdateRequest;

class ContactGroupController extends Controller
{
    protected ?string $accessEntity = 'contactGroups';

    public function index(): Response
    {
        $this->can('view');

        return Inertia::render('ContactGroups/Index', [
            'contactGroups' => ContactGroup::sorted()->paginate(10)->through(fn ($group) => [
                'id' => $group->id,
                'slug' => $group->slug,
                'name' => $group->name,
            ]),
            'canCreate' => Auth::user()->checkPermissionTo('create contactGroups'),
        ]);
    }

    public function create(): Response
    {
        $this->can('create');

        return Inertia::render('ContactGroups/Create', [
            //
        ]);
    }

    public function store(ContactGroupStoreRequest $request): RedirectResponse
    {
        $contactGroup = new ContactGroup();
        $contactGroup->fill($request->all());
        $contactGroup->created_by = Auth::id();
        $contactGroup->updated_by = Auth::id();

        if ($contactGroup->save()) {
            Session::flash('alert-success', trans('flash_message.contact_group.created'));

            return redirect()->route('contact_groups.index');
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_group.not_created'));

            return redirect()->route('contact_groups.create');
        }
    }

    public function show(ContactGroup $contactGroup): Response
    {
        $this->can('view');

        $user = Auth::user();
        $contacts = $contactGroup->contacts()->get(['contacts.id', 'contacts.slug', 'contacts.firstname', 'contacts.lastname']);

        return Inertia::render('ContactGroups/Show', [
            'contactGroup' => [
                'id' => $contactGroup->id,
                'slug' => $contactGroup->slug,
                'name' => $contactGroup->name,
            ],
            'contacts' => $contacts->map(fn ($contact) => [
                'id' => $contact->id,
                'slug' => $contact->slug,
                'fullname' => $contact->fullname,
            ]),
            'can' => [
                'edit' => $user->checkPermissionTo('edit contactGroups'),
                'delete' => $user->checkPermissionTo('delete contactGroups'),
            ],
        ]);
    }

    public function edit(ContactGroup $contactGroup): Response
    {
        $this->can('edit');

        return Inertia::render('ContactGroups/Edit', [
            'contactGroup' => [
                'id' => $contactGroup->id,
                'slug' => $contactGroup->slug,
                'name' => $contactGroup->name,
            ],
        ]);
    }

    public function update(ContactGroupUpdateRequest $request, ContactGroup $contactGroup): RedirectResponse
    {
        $contactGroup->fill($request->all());
        $contactGroup->updated_by = Auth::id();

        if ($contactGroup->save()) {
            Session::flash('alert-success', trans('flash_message.contact_group.updated'));

            return redirect()->route('contact_groups.show', [$contactGroup->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_group.not_updated'));

            return redirect()->route('contact_groups.edit', [$contactGroup->slug]);
        }
    }

    public function destroy(ContactGroup $contactGroup): RedirectResponse
    {
        $this->can('delete');

        if ($contactGroup->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_group.deleted'));

            return redirect()->route('contact_groups.index');
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_group.not_deleted'));

            return redirect()->route('contact_groups.delete', $contactGroup->slug);
        }
    }

    public function delete(ContactGroup $contactGroup): Response
    {
        $this->can('delete');

        return Inertia::render('ContactGroups/Delete', [
            'contactGroup' => [
                'id' => $contactGroup->id,
                'slug' => $contactGroup->slug,
                'name' => $contactGroup->name,
            ],
        ]);
    }
}
