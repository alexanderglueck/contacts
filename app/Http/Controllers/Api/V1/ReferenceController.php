<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\ContactGroup;
use App\Models\Country;
use App\Models\Gender;
use Illuminate\Http\JsonResponse;

/**
 * Read-only lookup tables the mobile app needs to populate dropdowns in
 * create/edit forms (gender, country, contact group). Highly cacheable on
 * the client — genders and countries are global static data; contact
 * groups change only when the user creates one.
 */
class ReferenceController extends Controller
{
    public function genders(): JsonResponse
    {
        return response()->json([
            'data' => Gender::orderBy('gender')
                ->get(['id', 'gender'])
                ->map(fn (Gender $g) => ['id' => $g->id, 'name' => $g->gender])
                ->values(),
        ]);
    }

    public function countries(): JsonResponse
    {
        return response()->json([
            'data' => Country::orderBy('country')
                ->get(['id', 'country'])
                ->map(fn (Country $c) => ['id' => $c->id, 'name' => $c->country])
                ->values(),
        ]);
    }

    public function contactGroups(): JsonResponse
    {
        // Tenant-scoped via the BelongsToTenantScope global scope on
        // ContactGroup (driven by SetTenant middleware on the route).
        return response()->json([
            'data' => ContactGroup::orderBy('name')
                ->get(['id', 'name'])
                ->map(fn (ContactGroup $g) => ['id' => $g->id, 'name' => $g->name])
                ->values(),
        ]);
    }
}
