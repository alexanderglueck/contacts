<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactImport\ContactImportImportRequest;
use App\Imports\ContactsImport;
use App\Models\ContactGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;
use Inertia\Response;
use Maatwebsite\Excel\Facades\Excel;

class ContactImportController extends Controller
{
    protected ?string $accessEntity = 'import';

    public function index(): Response
    {
        $this->can('create');

        return Inertia::render('ContactImport/Index', [
            'contactGroups' => ContactGroup::sorted()->get(['id', 'name']),
        ]);
    }

    public function import(ContactImportImportRequest $request, ContactsImport $contactsImport): RedirectResponse
    {
        if ($request->hasFile('import_file') && $request->file('import_file')->isValid()) {
            $contactsImport->setContactGroup($request->contact_group_id);
            Excel::import($contactsImport, $request->file('import_file'));

            Session::flash('alert-success', 'Contacts imported successfully.');

            return redirect()->route('import.index');
        }

        Session::flash('alert-danger', 'Could not import contacts.');

        return redirect()->route('import.index');
    }
}
