<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactImport\ContactImportImportRequest;
use App\Imports\ContactsImport;
use App\Models\ContactGroup;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;

class ContactImportController extends Controller
{
    protected ?string $accessEntity = 'import';

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $this->can('create');

        return view('contact_import.index', [
            'contactGroups' => ContactGroup::sorted()->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param ContactImportImportRequest $request
     *
     * @return \Illuminate\Http\Response
     */
    public function import(ContactImportImportRequest $request, ContactsImport $contactsImport)
    {
        if ($request->hasFile('import_file') && $request->file('import_file')->isValid()) {

            $contactsImport->setContactGroup($request->contact_group_id);
            Excel::import($contactsImport, $request->file('import_file'));

            Session::flash('alert-success', 'Kontakte wurden erfolgreich importiert!');

            return redirect()->route('import.index');
        } else {
            Session::flash('alert-danger', 'Kontakte konnten nicht importiert werden!');

            return redirect()->route('import.index');
        }
    }
}
