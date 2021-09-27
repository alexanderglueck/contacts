<?php

namespace App\Imports\Sheets;

use App\Imports\MappingHolder;
use App\Models\ContactEmail;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;

class EmailsSheet implements ToModel
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

        $import = new ContactEmail([
            'name' => $row[1],
            'email' => $row[2],
        ]);

        $import->created_by = Auth::id();
        $import->updated_by = Auth::id();
        $import->contact_id = $this->mappingHolder->mapping[$row[0]];

        return $import;
    }

}
