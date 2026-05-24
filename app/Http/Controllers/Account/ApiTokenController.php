<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Laravel\Sanctum\PersonalAccessToken;

class ApiTokenController extends Controller
{
    public function index(Request $request): Response
    {
        $tokens = $request->user()
            ->tokens()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (PersonalAccessToken $t) => [
                'id' => $t->id,
                'name' => $t->name,
                'last_used_at' => $t->last_used_at?->diffForHumans(),
                'created_at' => $t->created_at->diffForHumans(),
            ]);

        return Inertia::render('UserSettings/ApiToken', [
            'tokens' => $tokens->values(),
            // 'newToken' is flashed once by store() — plaintext is only
            // returned to the client at creation time. Subsequent visits
            // see nothing here.
            'newToken' => $request->session()->get('new_api_token'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $token = $request->user()->createToken($request->input('name'));

        return redirect()
            ->route('user_settings.api_token.index')
            ->with('new_api_token', [
                'name' => $request->input('name'),
                'plain_text' => $token->plainTextToken,
            ]);
    }

    public function destroy(Request $request, $token): RedirectResponse
    {
        $request->user()
            ->tokens()
            ->where('id', $token)
            ->delete();

        return redirect()
            ->route('user_settings.api_token.index')
            ->with('alert-success', 'Token revoked.');
    }
}
