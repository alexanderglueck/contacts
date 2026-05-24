<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Tighten\Ziggy\Ziggy;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => fn () => $request->user()
                    ? [
                        'id' => $request->user()->id,
                        'name' => $request->user()->name,
                        'email' => $request->user()->email,
                        'slug' => $request->user()->slug,
                        'image' => $request->user()->image,
                        'current_team_id' => $request->user()->current_team_id,
                    ]
                    : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('alert-success'),
                'error' => fn () => $request->session()->get('alert-danger'),
                'info' => fn () => $request->session()->get('alert-info'),
                'warning' => fn () => $request->session()->get('alert-warning'),
            ],
            'ziggy' => fn () => [
                ...(new Ziggy)->toArray(),
                'location' => $request->url(),
            ],
        ];
    }
}
