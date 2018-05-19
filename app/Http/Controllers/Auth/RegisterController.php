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
     */
    protected function registered(Request $request, $user)
    {
        $this->guard()->logout();

        $team = Team::make([
            'name' => $user['name']
        ]);

        $team->owner_id = $user->id;
        $team->save();

        session()->put('tenant', $team->uuid);

        $user->teams()->attach($team->id);

        $user->current_team_id = $team->id;
        $user->save();

        event(new TenantWasCreated($team, $user));

        event(new UserSignedUp($user));

        flashSuccess('Please check your email for an activation link.');

        return redirect($this->redirectPath());
    }
}
