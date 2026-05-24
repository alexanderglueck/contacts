<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactEmail\ContactEmailStoreRequest;
use App\Http\Requests\ContactEmail\ContactEmailUpdateRequest;
use App\Models\Contact;
use App\Models\ContactEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class ContactEmailController extends Controller
{
    protected ?string $accessEntity = 'emails';

    public function index(Contact $contact): Response
    {
        $this->can('view');

        return Inertia::render('ContactEmails/Index', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'items' => $contact->emails->map(fn ($e) => [
                'id' => $e->id,
                'ulid' => $e->ulid,
                'name' => $e->name,
                'email' => $e->email,
            ]),
            'canCreate' => Auth::user()->checkPermissionTo('create emails'),
        ]);
    }

    public function create(Contact $contact): Response
    {
        $this->can('create');

        return Inertia::render('ContactEmails/Create', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
        ]);
    }

    public function store(ContactEmailStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->emails()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_email.created'));

            return redirect()->route('contact_emails.index', [$contact->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_email.not_created'));

            return redirect()->route('contact_emails.create', [$contact->ulid]);
        }
    }

    public function show(Contact $contact, ContactEmail $contactEmail): Response
    {
        $this->can('view');

        $user = Auth::user();

        return Inertia::render('ContactEmails/Show', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactEmail->id,
                'ulid' => $contactEmail->ulid,
                'name' => $contactEmail->name,
                'email' => $contactEmail->email,
            ],
            'can' => [
                'edit' => $user->checkPermissionTo('edit emails'),
                'delete' => $user->checkPermissionTo('delete emails'),
            ],
        ]);
    }

    public function edit(Contact $contact, ContactEmail $contactEmail): Response
    {
        $this->can('edit');

        return Inertia::render('ContactEmails/Edit', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactEmail->id,
                'ulid' => $contactEmail->ulid,
                'name' => $contactEmail->name,
                'email' => $contactEmail->email,
            ],
        ]);
    }

    public function update(ContactEmailUpdateRequest $request, Contact $contact, ContactEmail $contactEmail): RedirectResponse
    {
        if ($contactEmail->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_email.updated'));

            return redirect()->route('contact_emails.show', [$contact->ulid, $contactEmail->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_email.not_updated'));

            return redirect()->route('contact_emails.edit', [$contact->ulid, $contactEmail->ulid]);
        }
    }

    public function destroy(Contact $contact, ContactEmail $contactEmail): RedirectResponse
    {
        $this->can('delete');

        if ($contactEmail->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_email.deleted'));

            return redirect()->route('contact_emails.index', [$contact->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_email.not_deleted'));

            return redirect()->route('contact_emails.delete', [$contact->ulid, $contactEmail->ulid]);
        }
    }

    public function delete(Contact $contact, ContactEmail $contactEmail): Response
    {
        $this->can('delete');

        return Inertia::render('ContactEmails/Delete', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactEmail->id,
                'ulid' => $contactEmail->ulid,
                'name' => $contactEmail->name,
                'email' => $contactEmail->email,
            ],
        ]);
    }
}
