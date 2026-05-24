<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactAddress\ContactAddressStoreRequest;
use App\Http\Requests\ContactAddress\ContactAddressUpdateRequest;
use App\Models\Contact;
use App\Models\ContactAddress;
use App\Models\Country;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class ContactAddressController extends Controller
{
    protected ?string $accessEntity = 'addresses';

    public function index(Contact $contact): Response
    {
        $this->can('view');

        return Inertia::render('ContactAddresses/Index', [
            'contact' => ['slug' => $contact->slug, 'fullname' => $contact->fullname],
            'items' => $contact->addresses->map(fn ($a) => [
                'id' => $a->id,
                'slug' => $a->slug,
                'name' => $a->name,
                'street' => $a->street,
                'zip' => $a->zip,
                'city' => $a->city,
                'latitude' => $a->latitude,
                'longitude' => $a->longitude,
            ]),
            'canCreate' => Auth::user()->checkPermissionTo('create addresses'),
        ]);
    }

    public function create(Contact $contact): Response
    {
        $this->can('create');

        return Inertia::render('ContactAddresses/Create', [
            'contact' => ['slug' => $contact->slug, 'fullname' => $contact->fullname],
            'countries' => Country::all(['id', 'country']),
        ]);
    }

    public function store(ContactAddressStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->addresses()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_address.created'));

            return redirect()->route('contact_addresses.index', [$contact->slug]);
        }

        Session::flash('alert-danger', trans('flash_message.contact_address.not_created'));

        return redirect()->route('contact_addresses.create', [$contact->slug]);
    }

    public function show(Contact $contact, ContactAddress $contactAddress): Response
    {
        $this->can('view');

        $contactAddress->load('country:id,country');

        $user = Auth::user();

        return Inertia::render('ContactAddresses/Show', [
            'contact' => ['slug' => $contact->slug, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactAddress->id,
                'slug' => $contactAddress->slug,
                'name' => $contactAddress->name,
                'street' => $contactAddress->street,
                'zip' => $contactAddress->zip,
                'city' => $contactAddress->city,
                'state' => $contactAddress->state,
                'country' => $contactAddress->country?->country,
                'latitude' => $contactAddress->latitude,
                'longitude' => $contactAddress->longitude,
            ],
            'can' => [
                'edit' => $user->checkPermissionTo('edit addresses'),
                'delete' => $user->checkPermissionTo('delete addresses'),
            ],
        ]);
    }

    public function edit(Contact $contact, ContactAddress $contactAddress): Response
    {
        $this->can('edit');

        return Inertia::render('ContactAddresses/Edit', [
            'contact' => ['slug' => $contact->slug, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactAddress->id,
                'slug' => $contactAddress->slug,
                'name' => $contactAddress->name,
                'street' => $contactAddress->street,
                'zip' => $contactAddress->zip,
                'city' => $contactAddress->city,
                'state' => $contactAddress->state,
                'country_id' => $contactAddress->country_id,
                'latitude' => $contactAddress->latitude,
                'longitude' => $contactAddress->longitude,
            ],
            'countries' => Country::all(['id', 'country']),
        ]);
    }

    public function update(ContactAddressUpdateRequest $request, Contact $contact, ContactAddress $contactAddress): RedirectResponse
    {
        if ($contactAddress->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_address.updated'));

            return redirect()->route('contact_addresses.show', [$contact->slug, $contactAddress->slug]);
        }

        Session::flash('alert-danger', trans('flash_message.contact_address.not_updated'));

        return redirect()->route('contact_addresses.edit', [$contact->slug, $contactAddress->slug]);
    }

    public function destroy(Contact $contact, ContactAddress $contactAddress): RedirectResponse
    {
        $this->can('delete');

        if ($contactAddress->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_address.deleted'));

            return redirect()->route('contact_addresses.index', [$contact->slug]);
        }

        Session::flash('alert-danger', trans('flash_message.contact_address.not_deleted'));

        return redirect()->route('contact_addresses.delete', [$contact->slug, $contactAddress->slug]);
    }

    public function delete(Contact $contact, ContactAddress $contactAddress): Response
    {
        $this->can('delete');

        return Inertia::render('ContactAddresses/Delete', [
            'contact' => ['slug' => $contact->slug, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactAddress->id,
                'slug' => $contactAddress->slug,
                'name' => $contactAddress->name,
                'street' => $contactAddress->street,
                'city' => $contactAddress->city,
            ],
        ]);
    }
}
