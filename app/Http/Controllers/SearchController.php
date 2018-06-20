<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Returns all the contacts that matched the search criteria.
     *
     * @param Request $request
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function search(Request $request)
    {
        $contacts = Contact::search($request->post('search'))->paginate();

        return view('contact.index', [
            'contacts' => $contacts
        ]);
    }
}
