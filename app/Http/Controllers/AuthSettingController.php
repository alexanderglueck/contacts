<?php

namespace App\Http\Controllers;

use Auth;
use Session;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class AuthSettingController extends Controller
{
    private $validationRules = [
        'secret' => 'required|digits:6',
    ];

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, Google2FA $google2fa)
    {
        // Check if 2FA is already enabled
        if (Auth::user()->google2fa_secret == null) {

            // Check if the user got redirected back from the enable route
            // (google2fa_secret session key available)
            if ($request->session()->has('google2fa_secret')) {

                // Generate a new QR code URL with the secret from the session
                $google2fa_url = $google2fa->getQRCodeGoogleUrl(
                    config('app.name'),
                    Auth::user()->email,
                    $request->session()->get('google2fa_secret')
                );

                return view('auth_settings.image', [
                    'image' => $google2fa_url
                ]);
            }

            // 2FA is not enabled
            // and no session key was found
            // show the default enable button
            return view('auth_settings.activate');
        }

        // The user already has 2fa enabled.
        // show backup codes and the disable button
        return view('auth_settings.edit', [
            'user' => Auth::user(),
            'backupCodes' => Auth::user()->backupCodes
        ]);
    }

    public function disable()
    {
        // disable 2fa
        Auth::user()->google2fa_secret = null;
        if ( ! Auth::user()->save()) {
            // save failed, show error, keep codes

            return redirect()->route('auth_settings.edit');
        }

        // delete backup codes
        Auth::user()->backupCodes()->delete();

        return redirect()->route('auth_settings.edit');
    }

    /**
     * Generate a secret and display the QR code
     * @param Request $request
     * @param Google2FA $google2fa
     * @return \Illuminate\Http\RedirectResponse
     */
    public function enable(Request $request, Google2FA $google2fa)
    {
        $request->session()->put('google2fa_secret', $google2fa->generateSecretKey(64));

        return redirect()->route('auth_settings.edit');
    }

    /**
     * Check if the entered code is valid for the given qr code,
     * only if valid enable 2fa. otherwise the contact is locked out
     *
     * @param Request $request
     * @param Google2FA $google2fa
     * @return array
     */
    public function check(Request $request, Google2FA $google2fa)
    {
        $requestData = $request->all();

        $request['secret'] = str_replace(' ', '', $requestData['secret']);

        $this->validate($request, $this->validationRules);

        $userSecret = $request->session()->get('google2fa_secret');
        $secret = $requestData['secret'];

        // validate if the entered key is correct
        if ($google2fa->verifyKey($userSecret, $secret)) {

            // user aktiviert 2 fa
            Auth::user()->google2fa_secret = $userSecret;
            if ( ! Auth::user()->save()) {
                // failed to save the secret
                // show the image again

                Session::flash('alert-danger', '2FA konnte nicht aktiviert werden!');

                return redirect()->route('auth_settings.edit');
            }

            $request->session()->remove('google2fa_secret');

            // 10 backup codes werden generiert
            for ($i = 0; $i < 10; $i++) {
                if ( ! Auth::user()->backupCodes()->create([
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

            return redirect()->route('auth_settings.edit');
        }

        // entered secret was not correct
        // let the user enter the secret again instead of enabling the
        // 2fa
        Session::flash('alert-danger', 'Code nicht korrekt!');

        return redirect()->route('auth_settings.edit');
    }
}
