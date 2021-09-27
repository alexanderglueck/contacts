<?php

namespace App\Imports\Sheets;

use App\Imports\MappingHolder;
use App\Models\ContactAddress;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;

class AddressesSheet implements ToModel
{
    public function __construct(
        protected MappingHolder $mappingHolder
    )
    {
    }

    public function model(array $row)
    {
        if ( ! $row || $row[0] == null) {
            return;
        }

        $import = new ContactAddress([
            'name' => $row[1],
            'street' => $row[2],
            'zip' => $row[3],
            'city' => $row[4],
            'state' => $row[5],
            'longitude' => $row[7],
            'latitude' => $row[8],
        ]);
        $import->country_id = Country::where('country', '=', $row[6])->first()->id;

        $import->created_by = Auth::id();
        $import->updated_by = Auth::id();
        $import->contact_id = $this->mappingHolder->mapping[$row[0]];
        return $import;
    }
}
