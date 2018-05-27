<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use App\Models\Gender;
use App\Models\Contact;
use App\Models\Country;
use App\Models\ContactUrl;
use App\Models\ContactDate;
use App\Models\ContactEmail;
use Illuminate\Http\Request;
use App\Models\ContactNumber;
use App\Models\ContactAddress;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Readers\LaravelExcelReader;
use Maatwebsite\Excel\Classes\LaravelExcelWorksheet;

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
            'contactGroups' => Auth::user()->contactGroups()->sorted()->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {
        $this->can('create');

        $this->validate($request, [
            'contact_group_id' => 'required|integer|exists:tenant.contact_groups,id',
            'import_file' => 'required|file|mimes:xlsx'
        ]);

        if ($request->hasFile('import_file') && $request->file('import_file')->isValid()) {
            $fileNameOriginal = $request->file('import_file')->storePublicly('import');

            Excel::load(storage_path('app/') . $fileNameOriginal, function (LaravelExcelReader $reader) use ($request) {
                $this->contactMatching = [];

                // reader methods
                $reader->each(function (LaravelExcelWorksheet $sheet) use ($request) {
                    switch ($sheet->getTitle()) {
                        case 'Kontakte':
                            // Loop through all rows
                            $sheet->each(function ($row) use ($request) {
                                $import = new Contact();
                                $import->fill($row->toArray());

                                $import->gender_id = Gender::where('gender', '=', $row->gender)->first()->id;

                                $import->created_by = Auth::user()->id;
                                $import->updated_by = Auth::user()->id;

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

                                $import->created_by = Auth::user()->id;
                                $import->updated_by = Auth::user()->id;
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
