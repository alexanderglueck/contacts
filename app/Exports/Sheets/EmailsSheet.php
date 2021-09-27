<?php

namespace App\Exports\Sheets;

use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class EmailsSheet implements FromArray, WithTitle
{
    public function __construct(protected Collection $contacts)
    {
    }

    public function array(): array
    {
        return $this->contacts->map(function($contact) {
            return  $contact->emails->map(function($email) {
                return [
                    'contact_id' => $email->contact_id,
                    'name' => $email->name,
                    'email' => $email->email,
                ];
            });
        })->toArray();
    }

    public function title(): string
    {
        return 'E-Mails';
    }
}
