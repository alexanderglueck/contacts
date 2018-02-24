<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;

class InstallController extends Controller
{
    public function index()
    {
        if (config('contacts.installed')) {
            return abort(404);
        }

        return view('setup.install.index');
    }
}
