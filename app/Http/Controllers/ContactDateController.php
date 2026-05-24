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
            'contact' => ['slug' => $contact->slug, 'fullname' => $contact->fullname],
            'items' => $contact->dates->map(fn ($d) => [
                'id' => $d->id,
                'slug' => $d->slug,
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
            'contact' => ['slug' => $contact->slug, 'fullname' => $contact->fullname],
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

            return redirect()->route('contact_dates.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_date.not_created'));

            return redirect()->route('contact_dates.create', [$contact->slug]);
        }
    }

    public function show(Contact $contact, ContactDate $contactDate): Response
    {
        $this->can('view');

        $user = Auth::user();

        return Inertia::render('ContactDates/Show', [
            'contact' => ['slug' => $contact->slug, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactDate->id,
                'slug' => $contactDate->slug,
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
            'contact' => ['slug' => $contact->slug, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactDate->id,
                'slug' => $contactDate->slug,
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

            return redirect()->route('contact_dates.show', [$contact->slug, $contactDate->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_date.not_updated'));

            return redirect()->route('contact_dates.edit', [$contact->slug, $contactDate->slug]);
        }
    }

    public function destroy(Contact $contact, ContactDate $contactDate): RedirectResponse
    {
        $this->can('delete');

        if ($contactDate->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_date.deleted'));

            return redirect()->route('contact_dates.index', [$contact->slug]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_date.not_deleted'));

            return redirect()->route('contact_dates.delete', [$contact->slug, $contactDate->slug]);
        }
    }

    public function delete(Contact $contact, ContactDate $contactDate): Response
    {
        $this->can('delete');

        return Inertia::render('ContactDates/Delete', [
            'contact' => ['slug' => $contact->slug, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactDate->id,
                'slug' => $contactDate->slug,
                'name' => $contactDate->name,
                'formatted_date' => $contactDate->formatted_date,
            ],
        ]);
    }
}
