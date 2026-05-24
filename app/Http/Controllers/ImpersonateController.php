<?php

namespace App\Http\Controllers;

use App\Http\Requests\Impersonate\ImpersonateStoreRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ImpersonateController extends Controller
{
    protected ?string $accessEntity = 'users';

    public function store(ImpersonateStoreRequest $request): RedirectResponse
    {
        $target = User::where('slug', $request->userSlug)->firstOrFail();

        abort_unless(
            $request->user()->currentTeam->hasUser($target),
            403,
            'Stop right there, criminal scum!'
        );

        $request->session()->put('impersonate', $target->id);

        return redirect()->route('home');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->session()->remove('impersonate');

        return redirect()->route('home');
    }
}
