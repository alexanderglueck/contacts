<?php

namespace App\Observers;

use App\Jobs\GeocodeContactAddress;
use App\Models\ContactAddress;

class ContactAddressObserver
{
    /**
     * Fields whose change should trigger a re-geocode.
     */
    private const GEOCODED_FIELDS = ['street', 'zip', 'city', 'state', 'country_id'];

    public function created(ContactAddress $address): void
    {
        if ($this->hasAnyAddressContent($address)) {
            GeocodeContactAddress::dispatch($address);
        }
    }

    public function updated(ContactAddress $address): void
    {
        if ($address->wasChanged(self::GEOCODED_FIELDS) && $this->hasAnyAddressContent($address)) {
            GeocodeContactAddress::dispatch($address);
        }
    }

    private function hasAnyAddressContent(ContactAddress $address): bool
    {
        foreach (self::GEOCODED_FIELDS as $field) {
            if (trim((string) $address->{$field}) !== '') {
                return true;
            }
        }

        return false;
    }
}
