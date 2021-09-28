<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\Impersonate\ImpersonateStoreRequest;

class ImpersonateController extends Controller
{
    protected ?string $accessEntity = 'users';

    public function store(ImpersonateStoreRequest $request): RedirectResponse
    {
        abort_unless($request->user()->currentTeam->hasUser(
            User::find($request->userId)
        ), 403, 'Stop right there, criminal scum!');

        $request->session()->put('impersonate', $request->userId);

        return redirect()->route('home');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->remove('impersonate');

        return redirect()->route('home');
    }
}
