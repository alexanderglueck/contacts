<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request): RedirectResponse
    {
        return redirect()->route('contacts.index', ['q' => $request->post('search')]);
    }
}
