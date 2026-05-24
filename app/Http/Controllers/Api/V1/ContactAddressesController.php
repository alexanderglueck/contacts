<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactAddressRequest;
use App\Http\Requests\Api\V1\UpdateContactAddressRequest;
use App\Models\Contact;
use App\Models\ContactAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ContactAddressesController extends Controller
{
    protected ?string $accessEntity = 'addresses';

    public function store(StoreContactAddressRequest $request, Contact $contact): JsonResponse
    {
        $this->can('create');

        // contact_addresses.state is non-nullable with no DB default; the web
        // form always sends '' via `present`. Coalesce when missing/null so
        // the API stays permissive without a schema change.
        $data = $request->validated();
        $data['state'] ??= '';

        // ContactAddressObserver dispatches GeocodeContactAddress on created,
        // so lat/long will populate asynchronously. The response below ships
        // them as null until the Nominatim job runs.
        $address = $contact->addresses()->create($data);

        return response()->json(['data' => $this->serialize($address)], 201);
    }

    public function update(UpdateContactAddressRequest $request, Contact $contact, ContactAddress $address): JsonResponse
    {
        $this->can('edit');

        // ContactAddressObserver re-geocodes on updated() when any of
        // street/zip/city/state/country_id change — handled by the observer,
        // we don't need to do anything special here.
        $address->fill($request->validated())->save();

        return response()->json(['data' => $this->serialize($address)]);
    }

    public function destroy(Contact $contact, ContactAddress $address): Response
    {
        $this->can('delete');

        $address->delete();

        return response()->noContent();
    }

    private function serialize(ContactAddress $a): array
    {
        $a->loadMissing('country:id,country');

        return [
            'ulid' => $a->ulid,
            'name' => $a->name,
            'street' => $a->street,
            'zip' => $a->zip,
            'city' => $a->city,
            'state' => $a->state,
            'country' => $a->country?->country,
            'latitude' => $a->latitude !== null ? (float) $a->latitude : null,
            'longitude' => $a->longitude !== null ? (float) $a->longitude : null,
        ];
    }
}
