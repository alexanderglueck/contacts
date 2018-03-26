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
        $contacts = Contact::whereRaw('
            (
                CONCAT( 
                    CASE WHEN title IS NULL  
                        THEN "" 
                        ELSE CONCAT(title, " ") 
                    END, 
                    firstname, 
                    " ", 
                    lastname, 
                    CASE WHEN title_after IS NULL 
                        THEN ""
                        ELSE CONCAT(", ", title_after)
                    END,
                    CASE WHEN nickname IS NULL 
                        THEN "" 
                        ELSE CONCAT(" (",nickname, ")") 
                    END
                )
            ) LIKE ?', ['%' . $request->get('search') . '%'])->active()->get();

        return view('contact.index', [
            'contacts' => $contacts
        ]);
    }
}
