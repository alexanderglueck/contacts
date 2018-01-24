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

    public function contacts(Request $request)
    {
        $this->validate($request, [
            'bounds' => 'required|string'
        ]);

        $rawBounds = explode(",", $request->bounds);

        $bounds = [];
        $bounds['sw_lat'] = $rawBounds[0];
        $bounds['sw_lng'] = $rawBounds[1];
        $bounds['ne_lat'] = $rawBounds[2];
        $bounds['ne_lng'] = $rawBounds[3];

        $contactAddresses = ContactAddress::whereNotNull("latitude")
            ->whereNotNull("longitude")
            ->join('contacts', 'contact_id', '=', 'contacts.id')
            ->where('active', 1)
            ->whereRaw("(CASE WHEN " . $bounds['ne_lat'] . " < " . $bounds['sw_lat'] . "
                    THEN latitude BETWEEN " . $bounds['ne_lat'] . " AND " . $bounds['sw_lat'] . "
                    ELSE latitude BETWEEN " . $bounds['sw_lat'] . " AND " . $bounds['ne_lat'] . "
            END) 
            AND
            (CASE WHEN " . $bounds['ne_lng'] . " < " . $bounds['sw_lng'] . "
                    THEN longitude BETWEEN " . $bounds['ne_lng'] . " AND " . $bounds['sw_lng'] . "
                    ELSE longitude BETWEEN " . $bounds['sw_lng'] . " AND " . $bounds['ne_lng'] . "
            END)")
            ->get();

        $markers = [];

        foreach ($contactAddresses as $contactAddress) {

            $tempArray = [
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
            ];

            $markers[] = $tempArray;

        }

        return response()->json($markers);
    }
}
