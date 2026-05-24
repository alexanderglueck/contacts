<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactNote\ContactNoteStoreRequest;
use App\Http\Requests\ContactNote\ContactNoteUpdateRequest;
use App\Models\Contact;
use App\Models\ContactNote;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;

class ContactNoteController extends Controller
{
    protected ?string $accessEntity = 'notes';

    public function index(Contact $contact): Response
    {
        $this->can('view');

        return Inertia::render('ContactNotes/Index', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'items' => $contact->notes->map(fn ($n) => [
                'id' => $n->id,
                'ulid' => $n->ulid,
                'name' => $n->name,
                'note' => $n->note,
            ]),
            'canCreate' => Auth::user()->checkPermissionTo('create notes'),
        ]);
    }

    public function create(Contact $contact): Response
    {
        $this->can('create');

        return Inertia::render('ContactNotes/Create', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
        ]);
    }

    public function store(ContactNoteStoreRequest $request, Contact $contact): RedirectResponse
    {
        if ($contact->notes()->create($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_note.created'));

            return redirect()->route('contact_notes.index', [$contact->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_note.not_created'));

            return redirect()->route('contact_notes.create', [$contact->ulid]);
        }
    }

    public function show(Contact $contact, ContactNote $contactNote): Response
    {
        $this->can('view');

        $user = Auth::user();

        return Inertia::render('ContactNotes/Show', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactNote->id,
                'ulid' => $contactNote->ulid,
                'name' => $contactNote->name,
                'note' => $contactNote->note,
            ],
            'can' => [
                'edit' => $user->checkPermissionTo('edit notes'),
                'delete' => $user->checkPermissionTo('delete notes'),
            ],
        ]);
    }

    public function edit(Contact $contact, ContactNote $contactNote): Response
    {
        $this->can('edit');

        return Inertia::render('ContactNotes/Edit', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactNote->id,
                'ulid' => $contactNote->ulid,
                'name' => $contactNote->name,
                'note' => $contactNote->note,
            ],
        ]);
    }

    public function update(ContactNoteUpdateRequest $request, Contact $contact, ContactNote $contactNote): RedirectResponse
    {
        if ($contactNote->update($request->all())) {
            Session::flash('alert-success', trans('flash_message.contact_note.updated'));

            return redirect()->route('contact_notes.show', [$contact->ulid, $contactNote->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_note.not_updated'));

            return redirect()->route('contact_notes.edit', [$contact->ulid, $contactNote->ulid]);
        }
    }

    public function destroy(Contact $contact, ContactNote $contactNote): RedirectResponse
    {
        $this->can('delete');

        if ($contactNote->delete()) {
            Session::flash('alert-success', trans('flash_message.contact_note.deleted'));

            return redirect()->route('contact_notes.index', [$contact->ulid]);
        } else {
            Session::flash('alert-danger', trans('flash_message.contact_note.not_deleted'));

            return redirect()->route('contact_notes.delete', [$contact->ulid, $contactNote->ulid]);
        }
    }

    public function delete(Contact $contact, ContactNote $contactNote): Response
    {
        $this->can('delete');

        return Inertia::render('ContactNotes/Delete', [
            'contact' => ['ulid' => $contact->ulid, 'fullname' => $contact->fullname],
            'item' => [
                'id' => $contactNote->id,
                'ulid' => $contactNote->ulid,
                'name' => $contactNote->name,
                'note' => $contactNote->note,
            ],
        ]);
    }
}
