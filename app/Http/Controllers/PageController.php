<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function termsOfService(): Response
    {
        return Inertia::render('Pages/TermsOfService');
    }
}
