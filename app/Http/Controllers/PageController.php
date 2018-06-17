<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function termsOfService()
    {
        return view('page.terms_of_service');
    }
}
