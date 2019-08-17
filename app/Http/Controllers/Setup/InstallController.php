<?php

namespace App\Http\Controllers\Setup;

use Illuminate\Http\Request;
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

    public function store(Request $request)
    {
        if (trim($request->maps_api_key) !== '') {
            $this->writeToEnv('GOOGLE_MAPS_GEOCODING_KEY', $request->maps_api_key);
        }

        if (trim($request->stripe_api_key) !== '') {
            $this->writeToEnv('STRIPE_KEY', $request->stripe_api_key);
        }

        if (trim($request->stripe_api_secret) !== '') {
            $this->writeToEnv('STRIPE_SECRET', $request->stripe_api_secret);
        }

        if (
            (config('contacts.googleMapsKey') || trim($request->maps_api_key) !== '')
            && (config('services.stripe.key') || trim($request->stripe_api_key) !== '')
            && (config('services.stripe.secret') || trim($request->stripe_api_secret) !== '')
        ) {
            $this->writeToEnv('CONTACTS_INSTALLED', 'true');

            return redirect('/')->with('alert-success', trans('install.saved'));
        }

        return back();
    }

    private function writeToEnv($key, $value)
    {
        $envFile = app()->environmentFilePath();

        file_put_contents($envFile, preg_replace(
            "/{$key}=(.*)/",
            "{$key}={$value}",
            file_get_contents($envFile)
        ));
    }
}
