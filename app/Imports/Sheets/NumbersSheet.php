<?php

namespace App\Imports\Sheets;

use App\Imports\MappingHolder;
use App\Models\ContactEmail;
use App\Models\ContactNumber;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithTitle;

class NumbersSheet implements ToModel
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

        $import = new ContactNumber([
            'name' => $row[1],
            'number' => $row[2],
        ]);


        $import->created_by = Auth::id();
        $import->updated_by = Auth::id();
        $import->contact_id = $this->mappingHolder->mapping[$row[0]];

        return $import;
    }
}
