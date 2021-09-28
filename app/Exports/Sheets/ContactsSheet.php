<?php

namespace App\Exports\Sheets;

use App\Models\Contact;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class ContactsSheet implements FromArray, WithTitle
{
    public function __construct(protected Collection $contacts)
    {
    }

    public function array(): array
    {
        return $this->contacts->map(function (Contact $contact) {
            return [
                'id' => $contact->id,
                'lastname' => $contact->lastname,
                'firstname' => $contact->firstname,
                'company' => $contact->company,
                'job' => $contact->job,
                'department' => $contact->department,
                'title' => $contact->title,
                'title_after' => $contact->title_after,
                'salutation' => $contact->salutation,
                'gender' => $contact->gender->gender,
                'nickname' => $contact->nickname,
                'image' => $contact->image,
            ];
        })->toArray();
    }

    public function title(): string
    {
        return 'Kontakte';
    }
}
