<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function termsOfService(): View
    {
        return view('page.terms_of_service');
    }
}
