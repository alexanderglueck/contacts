<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class WelcomeController extends Controller
{
    public function index(): RedirectResponse|View
    {
        if (auth()->check()) {
            return redirect()->route('home');
        }

        return view('welcome');
    }
}
