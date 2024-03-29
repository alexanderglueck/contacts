<?php

namespace App\Exports\Sheets;

use App\Models\Contact;
use App\Models\ContactNumber;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class NumbersSheet implements FromArray, WithTitle
{
    public function __construct(protected Collection $contacts)
    {
    }

    public function array(): array
    {
        return $this->contacts->map(function (Contact $contact) {
            return $contact->numbers->map(function (ContactNumber $number) {
                return [
                    'contact_id' => $number->contact_id,
                    'name' => $number->name,
                    'number' => $number->number,
                ];
            });
        })->toArray();
    }

    public function title(): string
    {
        return 'Nummern';
    }


}
