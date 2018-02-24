<?php

namespace App\Http\Controllers\Setup;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class InstallController extends Controller
{
    public function index()
    {
        if (config('contacts.installed')) {
            return abort(404);
        }

        if (DB::connection()->getDatabaseName()) {
            dd("working " . DB::connection()->getDatabaseName());
        } else {
            dd("dsldfsdf");
        }

        return view('setup.install.index', [

        ]);
    }
}
