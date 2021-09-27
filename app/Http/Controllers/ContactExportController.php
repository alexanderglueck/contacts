<?php

namespace App\Http\Controllers;

use App\Exports\ContactsExport;
use App\Models\ContactGroup;
use App\Http\Requests\ContactExport\ContactExportExportRequest;

class ContactExportController extends Controller
{
    protected $accessEntity = 'export';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->can('create');

        return view('contact_export.index', [
            'contactGroups' => ContactGroup::sorted()->get()
        ]);
    }

    public function export(ContactExportExportRequest $request)
    {
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
        $contacts = ContactGroup::find($request->contact_group_id)->contacts()->active()->sorted()->get();

        return (new ContactsExport($contacts))->download('contacts.xlsx');
    }
}
