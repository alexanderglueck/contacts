<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;

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
        Contact::reindex();

        // $contacts = Contact::searchByQuery(array('match' => array('lastname' => $request->post('search')))); // WORKS
        // $contacts = Contact::search($request->post('search'));
        // dd($contacts);
        $contacts = $this->elasticsearch($request->post('search'));

        dd($contacts);

        // dd($contacts);
        return view('contact.index', [
            'contacts' => $contacts
        ]);
    }

    public function elasticsearch($searchQuery, $perPage = 15, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);

        $items = Contact::searchByQuery(['match' => ['lastname' => $searchQuery]], null, null, $perPage, ($page - 1) * $perPage); // WORKS

        $items = collect($items);

        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }
}
