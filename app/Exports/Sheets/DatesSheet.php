<?php

namespace App\Exports\Sheets;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class DatesSheet implements FromArray, WithTitle
{
    public function __construct(protected Collection $contacts)
    {
    }

    public function array(): array
    {
        return $this->contacts->map(function ($contact) {
            return $contact->dates->map(function ($date) {
                return [
                    'contact_id' => $date->contact_id,
                    'name' => $date->name,
                    'date' => $date->formatted_date,
                    'skip_year' => $date->skip_year,
                ];
            });
        })->toArray();
    }

    public function title(): string
    {
        return 'Datumsangaben';
    }


}
