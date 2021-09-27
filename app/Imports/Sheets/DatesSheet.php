<?php

namespace App\Imports\Sheets;

use App\Imports\MappingHolder;
use App\Models\ContactAddress;
use App\Models\ContactDate;
use App\Models\Country;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;

class DatesSheet implements ToModel
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

        $import = new ContactDate([
            'name' => $row[1],
            'skip_year' => $row[3] ? 1 : 0,
        ]);

        if ($row[3]) {
            $import->date = $row[2] . '1900';
        } else {
            $import->date = $row[2];
        }

        $import->created_by = Auth::id();
        $import->updated_by = Auth::id();
        $import->contact_id = $this->mappingHolder->mapping[$row[0]];

        return $import;
    }

}
