<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
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
                // While impersonating, $request->user() is already the
                // impersonated user (see Impersonate middleware), so the banner
                // reads auth.user.name; this flag just toggles its visibility.
                'impersonating' => fn () => $request->session()->has('impersonate'),
                'user' => fn () => $request->user()
                    ? [
                        'id' => $request->user()->id,
                        'name' => $request->user()->name,
                        'email' => $request->user()->email,
                        'ulid' => $request->user()->ulid,
                        'image' => $request->user()->image,
                        'current_team_id' => $request->user()->current_team_id,
                    ]
                    : null,
                'is_subscribed' => fn () => $request->user()
                    ? $request->user()->hasSubscription()
                    : false,
                'subscription_cancelled' => fn () => $request->user()
                    ? $request->user()->hasCancelled()
                    : false,
                'can' => fn () => $request->user()
                    ? [
                        // Frontend uses snake_case keys; the underlying permission
                        // strings are space-separated (and partly camelCase for
                        // contactGroups). Map here so the Vue side stays consistent.
                        'view_contacts' => $request->user()->checkPermissionTo('view contacts'),
                        'view_contact_groups' => $request->user()->checkPermissionTo('view contactGroups'),
                        'view_calendar' => $request->user()->checkPermissionTo('view calendar'),
                        'view_map' => $request->user()->checkPermissionTo('view map'),
                    ]
                    : [],
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
            'locale' => fn () => App::getLocale(),
            'translations' => fn () => $this->loadTranslations(App::getLocale()),
        ];
    }

    /**
     * Load the per-page JS message catalog for the active locale.
     *
     * The catalog lives at resources/js/lang/{locale}.json so it's a
     * single source of truth for both server-shared props and the
     * vue-i18n bundle. Missing files quietly return [] so a freshly
     * added locale code doesn't 500 the app while its strings are
     * still being written.
     */
    private function loadTranslations(string $locale): array
    {
        $path = resource_path("js/lang/{$locale}.json");

        if (! file_exists($path)) {
            return [];
        }

        return json_decode(file_get_contents($path), true) ?: [];
    }
}
