<?php

namespace App\Exports\Sheets;

use App\Models\Contact;
use App\Models\ContactAddress;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class AddressesSheet implements FromArray, WithTitle
{

    public function __construct(protected Collection $contacts)
    {
    }

    public function array(): array
    {
        return $this->contacts->map(function (Contact $contact) {
            return $contact->addresses->map(function(ContactAddress $address) {
                return [
                    'contact_id' => $address->contact_id,
                    'name' => $address->name,
                    'street' => $address->street,
                    'zip' => $address->zip,
                    'city' => $address->city,
                    'state' => $address->state,
                    'country' => $address->country->country,
                    'longitude' => $address->longitude,
                    'latitude' => $address->latitude,
                ];
            });
        })->toArray();
    }

    public function title(): string
    {
        return 'Adressen';
    }


}
