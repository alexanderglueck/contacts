<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactCall\ContactCallStoreRequest;
use App\Http\Requests\ContactCall\ContactCallUpdateRequest;
use App\Models\Contact;
use App\Models\ContactCall;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class ContactCallController extends Controller
{
    protected ?string $accessEntity = 'calls';

    public function index(Contact $contact): Response
    {
        $this->can('view');

        return Inertia::render('ContactCalls/Index', [
            'contact' => ['slug' => $contact->slug, 'fullname' => $contact->fullname],
            'items' => $contact->calls->map(fn ($c) => [
                'id' => $c->id,
                'formatted_called_at' => $c->formatted_called_at,
                'note' => $c->note,
            ]),
            'canCreate' => Auth::user()->checkPermissionTo('create calls'),
        ]);
    }

    public function create(Contact $contact): Response
    {
        $this->can('create');

        return Inertia::render('ContactCalls/Create', [
            'contact' => ['slug' => $contact->slug, 'fullname' => $contact->fullname],
        ]);
    }

    public function store(ContactCallStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->calls()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_call.created'));

            return redirect()->route('contact_calls.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_call.not_created'));

            return redirect()->route('contact_calls.create', [$contact->slug]);
        }
    }

    public function show(Contact $contact, ContactCall $contactCall): Response
    {
        $this->can('view');

        $user = Auth::user();

        return Inertia::render('ContactCalls/Show', [
            'contact' => ['slug' => $contact->slug, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactCall->id,
                'formatted_called_at' => $contactCall->formatted_called_at,
                'note' => $contactCall->note,
            ],
            'can' => [
                'edit' => $user->checkPermissionTo('edit calls'),
                'delete' => $user->checkPermissionTo('delete calls'),
            ],
        ]);
    }

    public function edit(Contact $contact, ContactCall $contactCall): Response
    {
        $this->can('edit');

        return Inertia::render('ContactCalls/Edit', [
            'contact' => ['slug' => $contact->slug, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactCall->id,
                'called_at' => $contactCall->formatted_called_at,
                'note' => $contactCall->note,
            ],
        ]);
    }

    public function update(ContactCallUpdateRequest $request, Contact $contact, ContactCall $contactCall): RedirectResponse
    {
        if ($contactCall->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_call.updated'));

            return redirect()->route('contact_calls.show', [$contact->slug, $contactCall->id]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_call.not_updated'));

            return redirect()->route('contact_calls.edit', [$contact->slug, $contactCall->id]);
        }
    }

    public function destroy(Contact $contact, ContactCall $contactCall): RedirectResponse
    {
        $this->can('delete');

        if ($contactCall->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_call.deleted'));

            return redirect()->route('contact_calls.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_call.not_deleted'));

            return redirect()->route('contact_calls.delete', [$contact->slug, $contactCall->id]);
        }
    }

    public function delete(Contact $contact, ContactCall $contactCall): Response
    {
        $this->can('delete');

        return Inertia::render('ContactCalls/Delete', [
            'contact' => ['slug' => $contact->slug, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactCall->id,
                'formatted_called_at' => $contactCall->formatted_called_at,
                'note' => $contactCall->note,
            ],
        ]);
    }
}
