<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactNumber\ContactNumberStoreRequest;
use App\Http\Requests\ContactNumber\ContactNumberUpdateRequest;
use App\Models\Contact;
use App\Models\ContactNumber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class ContactNumberController extends Controller
{
    protected ?string $accessEntity = 'numbers';

    public function index(Contact $contact): Response
    {
        $this->can('view');

        return Inertia::render('ContactNumbers/Index', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'items' => $contact->numbers->map(fn ($n) => [
                'id' => $n->id,
                'ulid' => $n->ulid,
                'name' => $n->name,
                'number' => $n->number,
            ]),
            'canCreate' => Auth::user()->checkPermissionTo('create numbers'),
        ]);
    }

    public function create(Contact $contact): Response
    {
        $this->can('create');

        return Inertia::render('ContactNumbers/Create', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
        ]);
    }

    public function store(ContactNumberStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->numbers()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_number.created'));

            return redirect()->route('contact_numbers.index', [$contact->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_number.not_created'));

            return redirect()->route('contact_numbers.create', [$contact->ulid]);
        }
    }

    public function show(Contact $contact, ContactNumber $contactNumber): Response
    {
        $this->can('view');

        $user = Auth::user();

        return Inertia::render('ContactNumbers/Show', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactNumber->id,
                'ulid' => $contactNumber->ulid,
                'name' => $contactNumber->name,
                'number' => $contactNumber->number,
            ],
            'can' => [
                'edit' => $user->checkPermissionTo('edit numbers'),
                'delete' => $user->checkPermissionTo('delete numbers'),
            ],
        ]);
    }

    public function edit(Contact $contact, ContactNumber $contactNumber): Response
    {
        $this->can('edit');

        return Inertia::render('ContactNumbers/Edit', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactNumber->id,
                'ulid' => $contactNumber->ulid,
                'name' => $contactNumber->name,
                'number' => $contactNumber->number,
            ],
        ]);
    }

    public function update(ContactNumberUpdateRequest $request, Contact $contact, ContactNumber $contactNumber): RedirectResponse
    {
        if ($contactNumber->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_number.updated'));

            return redirect()->route('contact_numbers.show', [$contact->ulid, $contactNumber->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_number.not_updated'));

            return redirect()->route('contact_numbers.edit', [$contact->ulid, $contactNumber->ulid]);
        }
    }

    public function destroy(Contact $contact, ContactNumber $contactNumber): RedirectResponse
    {
        $this->can('delete');

        if ($contactNumber->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_number.deleted'));

            return redirect()->route('contact_numbers.index', [$contact->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_number.not_deleted'));

            return redirect()->route('contact_numbers.delete', [$contact->ulid, $contactNumber->ulid]);
        }
    }

    public function delete(Contact $contact, ContactNumber $contactNumber): Response
    {
        $this->can('delete');

        return Inertia::render('ContactNumbers/Delete', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactNumber->id,
                'ulid' => $contactNumber->ulid,
                'name' => $contactNumber->name,
                'number' => $contactNumber->number,
            ],
        ]);
    }
}
