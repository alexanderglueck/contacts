<?php

namespace App\Http\Controllers;

use App\Models\ContactGroup;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\ContactGroup\ContactGroupStoreRequest;
use App\Http\Requests\ContactGroup\ContactGroupUpdateRequest;

class ContactGroupController extends Controller
{
    protected ?string $accessEntity = 'contactGroups';

    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $this->can('view');

        return view('contact_group.index', [
            'contactGroups' => ContactGroup::sorted()->paginate(10)
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $this->can('create');

        return view('contact_group.create', [
            'contactGroups' => ContactGroup::sorted()->get(),
            'contactGroup' => new ContactGroup()
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
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

    /**
     * Display the specified resource.
     */
    public function show(ContactGroup $contactGroup): View
    {
        $this->can('view');

        return view('contact_group.show', [
            'contactGroup' => $contactGroup,
            'contacts' => $contactGroup->contacts()->paginate(10)
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ContactGroup $contactGroup): View
    {
        $this->can('edit');

        return view('contact_group.edit', [
            'contactGroup' => $contactGroup,
            'contactGroups' => ContactGroup::sorted()->get(),
            'createButtonText' => 'Kontaktgruppe aktualisieren'
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
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

    /**
     * Remove the specified resource from storage.
     */
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

    /**
     * Show the form for deleting the specified resource.
     */
    public function delete(ContactGroup $contactGroup): View
    {
        return view('contact_group.delete', [
            'contactGroup' => $contactGroup
        ]);
    }
}
