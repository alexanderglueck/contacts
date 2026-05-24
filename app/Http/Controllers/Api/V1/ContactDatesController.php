<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreContactDateRequest;
use App\Http\Requests\Api\V1\UpdateContactDateRequest;
use App\Models\Contact;
use App\Models\ContactDate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class ContactDatesController extends Controller
{
    protected ?string $accessEntity = 'dates';

    public function store(StoreContactDateRequest $request, Contact $contact): JsonResponse
    {
        $this->can('create');

        $date = $contact->dates()->create($request->validated());

        return response()->json(['data' => $this->serialize($date)], 201);
    }

    public function update(UpdateContactDateRequest $request, Contact $contact, ContactDate $date): JsonResponse
    {
        $this->can('edit');

        $date->fill($request->validated())->save();

        return response()->json(['data' => $this->serialize($date)]);
    }

    public function destroy(Contact $contact, ContactDate $date): Response
    {
        $this->can('delete');

        $date->delete();

        return response()->noContent();
    }

    private function serialize(ContactDate $d): array
    {
        return [
            'ulid' => $d->ulid,
            'name' => $d->name,
            'date' => $d->date ? substr($d->date, 0, 10) : null,
            'skip_year' => (bool) $d->skip_year,
        ];
    }
}
