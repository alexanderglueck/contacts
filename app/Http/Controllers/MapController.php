<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\ContactAddress;
use Illuminate\Http\Request;

class MapController extends Controller
{
    /**
     * Display all the geocoded contacts on a map.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('map.index', []);
    }

    public function contacts()
    {
        $contactAddresses = ContactAddress::whereNotNull("latitude")->whereNotNull("longitude")->join('contacts', 'contact_id', '=', 'contacts.id')->where('active', 1)->get();

        $markers = array();

        foreach ($contactAddresses as $contactAddress) {

            $tempArray = array(
                "title" => $contactAddress->contact->fullname,
                "name" => $contactAddress->name,
                "latitude" => $contactAddress->latitude,
                "longitude" => $contactAddress->longitude,
                "address" => "<p><b>" . $contactAddress->contact->fullname . "</b><br />" . PHP_EOL .
                    "<b>" . $contactAddress->name . "</b><br />" . PHP_EOL .
                    $contactAddress->street . "<br />" . PHP_EOL .
                    $contactAddress->zip . ", " . $contactAddress->city . "<br />" . PHP_EOL .
                    $contactAddress->state . "<br />" . PHP_EOL .
                    $contactAddress->country->country . "<br />" . PHP_EOL .
                    "<br />" . PHP_EOL .
                    "<a href='" . route('contacts.show', $contactAddress->contact->slug) . "'>Kontakt anzeigen</a></p>"
            );

            $markers[] = $tempArray;

        }

        return response()->json($markers);
    }
}
