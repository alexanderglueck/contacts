<?php

namespace App\Http\Controllers\Account;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Account\TwoFactorCheckRequest;

class TwoFactorController extends Controller
{
    /**
     * Show the form for editing the specified resource.
     *
     * @param Request   $request
     * @param Google2FA $google2fa
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Google2FA $google2fa): View
    {
        // Check if 2FA is already enabled
        if ( ! $request->user()->hasTwoFactorAuthentication()) {

            // Check if the user got redirected back from the enable route
            // (google2fa_secret session key available)
            if ($request->session()->has('google2fa_secret')) {

                // Generate a new QR code URL with the secret from the session
                $google2fa_url = $google2fa->getQRCodeUrl(
                    config('app.name'),
                    $request->user()->email,
                    $request->session()->get('google2fa_secret')
                );

                return view('user_settings.two_factor.image', [
                    'image' => $google2fa_url
                ]);
            }

            // 2FA is not enabled
            // and no session key was found
            // show the default enable button
            return view('user_settings.two_factor.activate');
        }

        // The user already has 2fa enabled.
        // show backup codes and the disable button
        return view('user_settings.two_factor.edit', [
            'user' => $request->user(),
            'backupCodes' => $request->user()->backupCodes
        ]);
    }

    public function disable(Request $request): RedirectResponse
    {
        // disable 2fa
        $request->user()->google2fa_secret = null;
        if ( ! $request->user()->save()) {
            // save failed, show error, keep codes

            return redirect()->route('user_settings.two_factor.edit');
        }

        // delete backup codes
        $request->user()->backupCodes()->delete();

        return redirect()->route('user_settings.two_factor.edit');
    }

    /**
     * Generate a secret and display the QR code
     *
     * @param Request   $request
     * @param Google2FA $google2fa
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enable(Request $request, Google2FA $google2fa): RedirectResponse
    {
        $request->session()->put('google2fa_secret', $google2fa->generateSecretKey(64));

        return redirect()->route('user_settings.two_factor.edit');
    }

    /**
     * Check if the entered code is valid for the given qr code,
     * only if valid enable 2fa. otherwise the contact is locked out
     *
     * @param TwoFactorCheckRequest $request
     * @param Google2FA             $google2fa
     *
     * @throws \Exception
     */
    public function check(TwoFactorCheckRequest $request, Google2FA $google2fa): RedirectResponse
    {
        $secret = str_replace(' ', '', $request->secret);

        $userSecret = $request->session()->get('google2fa_secret');

        // validate if the entered key is correct
        if ($google2fa->verifyKey($userSecret, $secret)) {

            // user aktiviert 2 fa
            $request->user()->google2fa_secret = $userSecret;
            if ( ! $request->user()->save()) {
                // failed to save the secret
                // show the image again

                Session::flash('alert-danger', '2FA konnte nicht aktiviert werden!');

                return redirect()->route('user_settings.two_factor.edit');
            }

            $request->session()->remove('google2fa_secret');

            // 10 backup codes werden generiert
            for ($i = 0; $i < 10; $i++) {
                if ( ! $request->user()->backupCodes()->create([
                    'value' => random_int(100000, 999999)
                ])
                ) {
                    $i--;
                }
            }

            // backup codes weggespeichert, bei verwendung werden sie rausgelöscht
            // wenn eingegebener code, in array noch unverbrauchte 2fa backup codes
            // ODER der richtige code valide is, dann login true

            // return true, continue login
            Session::flash('alert-success', '2FA wurde aktiviert!');

            return redirect()->route('user_settings.two_factor.edit');
        }

        // entered secret was not correct
        // let the user enter the secret again instead of enabling the
        // 2fa
        Session::flash('alert-danger', 'Code nicht korrekt!');

        return redirect()->route('user_settings.two_factor.edit');
    }
}
