<?php

namespace App\Http\Controllers\Setup;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Inertia\Inertia;
use Inertia\Response;

class InstallController extends Controller
{
    public function index(): Response
    {
        if (config('contacts.installed')) {
            abort(404);
        }

        return Inertia::render('Setup/Install', [
            'stripeKeyPresent' => trim((string) config('services.stripe.key')) !== ''
                && trim((string) config('services.stripe.secret')) !== '',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (trim($request->stripe_api_key) !== '') {
            $this->writeToEnv('STRIPE_KEY', $request->stripe_api_key);
        }

        if (trim($request->stripe_api_secret) !== '') {
            $this->writeToEnv('STRIPE_SECRET', $request->stripe_api_secret);
        }

        if (
            (config('services.stripe.key') || trim($request->stripe_api_key) !== '')
            && (config('services.stripe.secret') || trim($request->stripe_api_secret) !== '')
        ) {
            $this->writeToEnv('CONTACTS_INSTALLED', 'true');

            Artisan::call('storage:link');
            Artisan::call('route:cache');
            Artisan::call('config:cache');

            return redirect()->route('welcome')->with('alert-success', trans('install.saved'));
        }

        return back();
    }

    private function writeToEnv($key, $value): void
    {
        $envFile = app()->environmentFilePath();

        file_put_contents($envFile, preg_replace(
            "/{$key}=(.*)/",
            "{$key}={$value}",
            file_get_contents($envFile)
        ));
    }
}
