<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class ImpersonateController extends Controller
{
    protected $accessEntity = 'users';

    public function store(Request $request)
    {
        $this->can('impersonate');

        $this->validate($request, [
            'userId' => 'required|exists:system.users,id'
        ]);

        abort_unless(auth()->user()->currentTeam->hasUser(
            User::find($request->userId)
        ), 403, 'Stop right there, criminal scum!');

        $request->session()->put('impersonate', $request->userId);

        return redirect()->route('home');
    }

    public function destroy(Request $request)
    {
        $request->session()->remove('impersonate');

        return redirect()->route('home');
    }
}
