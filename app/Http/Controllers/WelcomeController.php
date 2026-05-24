<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    public function index(): RedirectResponse|Response
    {
        if (auth()->check()) {
            return redirect()->route('home');
        }

        return Inertia::render('Welcome', [
            'canLogin' => true,
            'canRegister' => true,
        ]);
    }
}
