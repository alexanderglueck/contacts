<?php

namespace App\Http\Controllers\Auth;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\Auth\UserSignedUp;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Events\Tenant\TenantWasCreated;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'terms' => 'required'
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     *
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
    }

    /**
     * @param Request $request
     * @param User    $user
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    protected function registered(Request $request, $user)
    {
        /*
         * Do not immediately sign-in the user
         */
        $this->guard()->logout();

        /*
         * Create a new team
         */
        $team = Team::create([
            'name' => $user['name'],
            'owner_id' => $user->id
        ]);

        /*
         * Attach the user with the team.
         * This adds the user as a team member and sets the current_team_id to
         * the given team
         */
        $user->attachTeam($team);

        /*
         * This starts all processes that should run when a tenant is created
         */
        event(new TenantWasCreated($team, $user));

        flashSuccess('Please check your email for an activation link.');

        return redirect($this->redirectPath());
    }
}
