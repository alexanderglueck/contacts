<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use App\Models\Contact;
use App\Models\Country;
use App\Models\ContactUrl;
use App\Models\ContactDate;
use App\Models\ContactEmail;
use App\Models\ContactGroup;
use App\Models\ContactNumber;
use App\Models\ContactAddress;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\ContactImport\ContactImportImportRequest;

class ContactImportController extends Controller
{
    protected $accessEntity = 'import';

    private $contactMatching;

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
    public function import(ContactImportImportRequest $request)
    {
        if ($request->hasFile('import_file') && $request->file('import_file')->isValid()) {
            $fileNameOriginal = $request->file('import_file')->storePublicly('import');

            Excel::load(storage_path('app/') . $fileNameOriginal, function ( $reader) use ($request) {
                $this->contactMatching = [];

                // reader methods
                $reader->each(function ( $sheet) use ($request) {
                    switch ($sheet->getTitle()) {
                        case 'Kontakte':
                            // Loop through all rows
                            $sheet->each(function ($row) use ($request) {
                                $import = new Contact();
                                $import->fill($row->toArray());

                                $import->gender_id = Gender::where('gender', '=', $row->gender)->first()->id;

                                $import->created_by = Auth::id();
                                $import->updated_by = Auth::id();

                                if ($import->save()) {
                                    // saved

                                    $importId = $import->id;

                                    $this->contactMatching[$row->id] = $importId;

                                    $import->contactGroups()->attach($importId, [
                                        'contact_group_id' => $request->contact_group_id
                                    ]);
                                } else {
                                    // not saved
                                }
                            });
                            break;
                        case 'Adressen':
                        case 'Datumsangaben':
                        case 'E-Mails':
                        case 'Nummern':
                        case 'Websiten':
                            $sheet->each(function ($row) use ($sheet) {
                                $import = $this->getTypeFromName($sheet->getTitle());

                                if ($sheet->getTitle() == 'Datumsangaben') {
                                    if ($row->skip_year) {
                                        $row['date'] = $row->date . '1900';
                                    } else {
                                        $row['skip_year'] = 0;
                                    }
                                }

                                $import->fill($row->toArray());

                                if ($sheet->getTitle() == 'Adressen') {
                                    $import->country_id = Country::where('country', '=', $row->country)->first()->id;
                                }

                                $import->created_by = Auth::id();
                                $import->updated_by = Auth::id();
                                $import->contact_id = $this->contactMatching[$row->contact_id];

                                if ($import->save()) {
                                    // saved
                                } else {
                                    // not saved
                                }
                            });
                            break;
                        default:
                    }
                });
            });

            Session::flash('alert-success', 'Kontakte wurden erfolgreich importiert!');

            return redirect()->route('import.index');
        } else {
            Session::flash('alert-danger', 'Kontakte konnten nicht importiert werden!');

            return redirect()->route('import.index');
        }
    }

    private function getTypeFromName($title)
    {
        switch ($title) {
            case 'Adressen':
                return new ContactAddress();
            case 'Datumsangaben':
                return new ContactDate();
            case 'E-Mails':
                return new ContactEmail();
            case 'Nummern':
                return new ContactNumber();
            case 'Websiten':
                return new ContactUrl();
        }
    }
}
