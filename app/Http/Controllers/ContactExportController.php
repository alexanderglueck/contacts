<?php

namespace App\Http\Controllers;

use Auth;
use Maatwebsite\Excel\Facades\Excel;
use Response;
use App\Models\Gender;
use SplTempFileObject;
use App\Models\Contact;
use App\Models\Country;
// use League\Csv\Writer;
use Illuminate\Http\Request;
use JeroenDesloovere\VCard\VCard;

class ContactExportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('contact_export.index', [
            'contactGroups' => Auth::user()->contactGroups()->sorted()->get()
        ]);
    }

    public function export(Request $request)
    {
        $this->validate($request, [
            'contact_group_id' => 'required|integer|exists:contact_groups,id',
        ]);

        /**
         * CSV
         */
        /*
         * LEAGUE

        $csv = Writer::createFromFileObject(new SplTempFileObject());

        // The PDOStatement Object implements the Traversable Interface
        // that's why Writer::insertAll can directly insert
        // the data into the CSV
        $csv->insertAll(Contact::active()->sorted()->get()->toArray());

        // Because you are providing the filename you don't have to
        // set the HTTP headers Writer::output can
        // directly set them for you
        // The file is downloadable
        $csv->output('contacts.csv');
        die();
        */

        /**
         * JSON
         */
        /*
        return ["contacts" => Contact::active()->sorted()->get()];
        */

        /**
         * VCARD
         */
        /* $contact = Contact::active()->sorted()->first();

         $vcard = new VCard();

         $vcard->addName($contact->firstname, $contact->lastname);

         foreach($contact->emails as $email) {
             $vcard->addEmail($email->email);
         }

         foreach ($contact->addresses as $address) {
             $vcard->addAddress($address->name, '', $address->street, $address->city, $address->state, $address->zip, $address->country->country);
         }

         foreach ($contact->urls as $url) {
             $vcard->addURL($url->url, $url->name);
         }

         return Response::make($vcard->getOutput(), 200, $vcard->getHeaders(true)); */

        /**
         * Excel
         */
        $contacts = Auth::user()->contactGroups()->find($request->contact_group_id)->contacts()->active()->sorted()->get();

        Excel::create('contacts', function ($excel) use ($contacts) {

            // Set the title
            $excel->setTitle('Kontakt Export');

            // Our first sheet
            $excel->sheet('Kontakte', function ($sheet) use ($contacts) {
                $tempContacts = [];

                foreach ($contacts as $contact) {
                    $temp = [
                        'id' => $contact->id,
                        'lastname' => $contact->lastname,
                        'firstname' => $contact->firstname,
                        'company' => $contact->company,
                        'job' => $contact->job,
                        'department' => $contact->department,
                        'title' => $contact->title,
                        'title_after' => $contact->title_after,
                        'salutation' => $contact->salutation,
                        'gender' => $contact->gender->gender,
                        'nickname' => $contact->nickname,
                        'image' => $contact->image,
                    ];

                    array_push($tempContacts, $temp);
                }

                $sheet->fromArray($tempContacts);
            });

            // Our second sheet
            $excel->sheet('Adressen', function ($sheet) use ($contacts) {
                $tempAddresses = [];

                foreach ($contacts as $contact) {
                    foreach ($contact->addresses as $address) {
                        $temp = [
                            'contact_id' => $address->contact_id,
                            'name' => $address->name,
                            'street' => $address->street,
                            'zip' => $address->zip,
                            'city' => $address->city,
                            'state' => $address->state,
                            'country' => $address->country->country,
                            'longitude' => $address->longitude,
                            'latitude' => $address->latitude,
                        ];

                        array_push($tempAddresses, $temp);
                    }
                }

                $sheet->fromArray($tempAddresses);
            });

            // Our second sheet
            $excel->sheet('Datumsangaben', function ($sheet) use ($contacts) {
                $contactDates = [];

                foreach ($contacts as $contact) {
                    foreach ($contact->dates as $date) {
                        $temp = [
                            'contact_id' => $date->contact_id,
                            'name' => $date->name,
                            'date' => $date->formatted_date,
                            'skip_year' => $date->skip_year,
                        ];

                        array_push($contactDates, $temp);
                    }
                }

                $sheet->fromArray($contactDates);
            });

            // Our third sheet
            $excel->sheet('E-Mails', function ($sheet) use ($contacts) {
                $contactEmails = [];

                foreach ($contacts as $contact) {
                    foreach ($contact->emails as $email) {
                        $temp = [
                            'contact_id' => $email->contact_id,
                            'name' => $email->name,
                            'email' => $email->email,
                        ];

                        array_push($contactEmails, $temp);
                    }
                }

                $sheet->fromArray($contactEmails);
            });

            // Our fourth sheet
            $excel->sheet('Nummern', function ($sheet) use ($contacts) {
                $contactNumbers = [];

                foreach ($contacts as $contact) {
                    foreach ($contact->numbers as $number) {
                        $temp = [
                            'contact_id' => $number->contact_id,
                            'name' => $number->name,
                            'number' => $number->number,
                        ];

                        array_push($contactNumbers, $temp);
                    }
                }

                $sheet->fromArray($contactNumbers);
            });

            // Our fifth sheet
            $excel->sheet('Websiten', function ($sheet) use ($contacts) {
                $contactWebsites = [];

                foreach ($contacts as $contact) {
                    foreach ($contact->urls as $website) {
                        $temp = [
                            'contact_id' => $website->contact_id,
                            'name' => $website->name,
                            'url' => $website->url,
                        ];

                        array_push($contactWebsites, $temp);
                    }
                }

                $sheet->fromArray($contactWebsites);
            });
        })->download('xlsx');
    }
}
