<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Returns all the contacts that matched the search criteria.
     */
    public function search(Request $request): View
    {
        $search = $request->post('search');

        $contacts = Contact::search($search)->paginate();

        return view('contact.index', [
            'contacts' => $contacts,
            'search' => $search
        ]);
    }
}
