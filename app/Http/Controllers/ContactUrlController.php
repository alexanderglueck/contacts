<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactUrl\ContactUrlStoreRequest;
use App\Http\Requests\ContactUrl\ContactUrlUpdateRequest;
use App\Models\Contact;
use App\Models\ContactUrl;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class ContactUrlController extends Controller
{
    protected ?string $accessEntity = 'urls';

    public function index(Contact $contact): Response
    {
        $this->can('view');

        return Inertia::render('ContactUrls/Index', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'items' => $contact->urls->map(fn ($u) => [
                'id' => $u->id,
                'ulid' => $u->ulid,
                'name' => $u->name,
                'url' => $u->url,
            ]),
            'canCreate' => Auth::user()->checkPermissionTo('create urls'),
        ]);
    }

    public function create(Contact $contact): Response
    {
        $this->can('create');

        return Inertia::render('ContactUrls/Create', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
        ]);
    }

    public function store(ContactUrlStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->urls()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_url.created'));

            return redirect()->route('contact_urls.index', [$contact->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_url.not_created'));

            return redirect()->route('contact_urls.create', [$contact->ulid]);
        }
    }

    public function show(Contact $contact, ContactUrl $contactUrl): Response
    {
        $this->can('view');

        $user = Auth::user();

        return Inertia::render('ContactUrls/Show', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactUrl->id,
                'ulid' => $contactUrl->ulid,
                'name' => $contactUrl->name,
                'url' => $contactUrl->url,
            ],
            'can' => [
                'edit' => $user->checkPermissionTo('edit urls'),
                'delete' => $user->checkPermissionTo('delete urls'),
            ],
        ]);
    }

    public function edit(Contact $contact, ContactUrl $contactUrl): Response
    {
        $this->can('edit');

        return Inertia::render('ContactUrls/Edit', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactUrl->id,
                'ulid' => $contactUrl->ulid,
                'name' => $contactUrl->name,
                'url' => $contactUrl->url,
            ],
        ]);
    }

    public function update(ContactUrlUpdateRequest $request, Contact $contact, ContactUrl $contactUrl): RedirectResponse
    {
        if ($contactUrl->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_url.updated'));

            return redirect()->route('contact_urls.show', [$contact->ulid, $contactUrl->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_url.not_updated'));

            return redirect()->route('contact_urls.edit', [$contact->ulid, $contactUrl->ulid]);
        }
    }

    public function destroy(Contact $contact, ContactUrl $contactUrl): RedirectResponse
    {
        $this->can('delete');

        if ($contactUrl->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_url.deleted'));

            return redirect()->route('contact_urls.index', [$contact->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_url.not_deleted'));

            return redirect()->route('contact_urls.delete', [$contact->ulid, $contactUrl->ulid]);
        }
    }

    public function delete(Contact $contact, ContactUrl $contactUrl): Response
    {
        $this->can('delete');

        return Inertia::render('ContactUrls/Delete', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactUrl->id,
                'ulid' => $contactUrl->ulid,
                'name' => $contactUrl->name,
                'url' => $contactUrl->url,
            ],
        ]);
    }
}
