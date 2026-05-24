<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactDate\ContactDateStoreRequest;
use App\Http\Requests\ContactDate\ContactDateUpdateRequest;
use App\Models\Contact;
use App\Models\ContactDate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class ContactDateController extends Controller
{
    protected ?string $accessEntity = 'dates';

    public function index(Contact $contact): Response
    {
        $this->can('view');

        return Inertia::render('ContactDates/Index', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'items' => $contact->dates->map(fn ($d) => [
                'id' => $d->id,
                'ulid' => $d->ulid,
                'name' => $d->name,
                'formatted_date' => $d->formatted_date,
                'skip_year' => (bool) $d->skip_year,
            ]),
            'canCreate' => Auth::user()->checkPermissionTo('create dates'),
        ]);
    }

    public function create(Contact $contact): Response
    {
        $this->can('create');

        return Inertia::render('ContactDates/Create', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
        ]);
    }

    public function store(ContactDateStoreRequest $request, Contact $contact): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->skip_year) {
            $validated['date'] .= 1900;
        }

        if ($contact->contactDates()->create($validated)) {
            Session::flash('alert-success', trans('flash_message.contact_date.created'));

            return redirect()->route('contact_dates.index', [$contact->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_date.not_created'));

            return redirect()->route('contact_dates.create', [$contact->ulid]);
        }
    }

    public function show(Contact $contact, ContactDate $contactDate): Response
    {
        $this->can('view');

        $user = Auth::user();

        return Inertia::render('ContactDates/Show', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactDate->id,
                'ulid' => $contactDate->ulid,
                'name' => $contactDate->name,
                'formatted_date' => $contactDate->formatted_date,
                'skip_year' => (bool) $contactDate->skip_year,
            ],
            'can' => [
                'edit' => $user->checkPermissionTo('edit dates'),
                'delete' => $user->checkPermissionTo('delete dates'),
            ],
        ]);
    }

    public function edit(Contact $contact, ContactDate $contactDate): Response
    {
        $this->can('edit');

        return Inertia::render('ContactDates/Edit', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactDate->id,
                'ulid' => $contactDate->ulid,
                'name' => $contactDate->name,
                'date' => $contactDate->formatted_date,
                'skip_year' => (int) $contactDate->skip_year,
            ],
        ]);
    }

    public function update(ContactDateUpdateRequest $request, Contact $contact, ContactDate $contactDate): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->skip_year) {
            $validated['date'] .= 1900;
        }

        if ($contactDate->update($validated)) {
            Session::flash('alert-success', trans('flash_message.contact_date.updated'));

            return redirect()->route('contact_dates.show', [$contact->ulid, $contactDate->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_date.not_updated'));

            return redirect()->route('contact_dates.edit', [$contact->ulid, $contactDate->ulid]);
        }
    }

    public function destroy(Contact $contact, ContactDate $contactDate): RedirectResponse
    {
        $this->can('delete');

        if ($contactDate->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_date.deleted'));

            return redirect()->route('contact_dates.index', [$contact->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_date.not_deleted'));

            return redirect()->route('contact_dates.delete', [$contact->ulid, $contactDate->ulid]);
        }
    }

    public function delete(Contact $contact, ContactDate $contactDate): Response
    {
        $this->can('delete');

        return Inertia::render('ContactDates/Delete', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactDate->id,
                'ulid' => $contactDate->ulid,
                'name' => $contactDate->name,
                'formatted_date' => $contactDate->formatted_date,
            ],
        ]);
    }
}
