<?php

namespace App\Http\Controllers\Auth;

use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\TwoFactorFailure;
use App\Events\TwoFactorSuccess;
use PragmaRX\Google2FA\Google2FA;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'logout']);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  mixed                    $user
     *
     * @return mixed
     */
    protected function authenticated(Request $request, User $user)
    {
        if ( ! $user->isActivated()) {
            $this->guard()->logout();

            flashError('Your account is not activated.');

            return back();
        }

        if ($user->hasTwoFactorAuthentication()) {
            // logout
            Auth::logout();

            $request->session()->put('token-user-id', $user->id);
            $request->session()->put('token-remember', $request->has('remember'));

            return redirect()->route('login.token');
        }

        if ($user->currentTeam) {
            session()->put('tenant', $user->currentTeam->uuid);
        }
    }

    public function token(Request $request)
    {
        if ( ! $request->session()->has('token-user-id')) {
            Auth::logout();

            return redirect()->route('login');
        }

        return view('auth.token');
    }

    public function check(Request $request, Google2FA $google2fa)
    {
        if ( ! $request->session()->has('token-user-id')) {
            Auth::logout();

            return redirect()->route('login');
        }

        $request['token'] = str_replace(' ', '', $request->token);

        $this->validate($request, [
            'token' => 'required|digits:6',
        ]);

        $secret = Input::get('token');

        $user = User::find($request->session()->get('token-user-id'));

        $remember = $request->session()->get('token-remember');
        $request->session()->remove('token-remember');

        // validate if the entered key is correct
        $valid = $google2fa->verifyKey($user->google2fa_secret, $secret);

        if ($valid) {
            // return true, continue login

            Auth::loginUsingId($request->session()->get('token-user-id'), $remember);

            $request->session()->remove('token-user-id');

            event(new TwoFactorSuccess(Auth::user()));

            return redirect()->route('home');
        }

        // key was not valid according to google 2fa
        // check if it was a backup code
        // this check should  be after the verifyKey,
        // to ensure that the user does not loose backup codes on accident
        $keys = $user->backupCodes->pluck('value')->all();
        if (in_array($secret, $keys)) {
            $user->backupCodes()->where('value', $secret)->delete();

            Auth::loginUsingId($request->session()->get('token-user-id'), $remember);

            $request->session()->remove('token-user-id');

            event(new TwoFactorSuccess(Auth::user()));

            return redirect()->route('home');
        }

        event(new TwoFactorFailure(User::find($request->session()->get('token-user-id'))));

        return redirect()->route('login.token');
    }
}
