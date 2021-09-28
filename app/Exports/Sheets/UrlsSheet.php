<?php

namespace App\Exports\Sheets;

use App\Models\Contact;
use App\Models\ContactUrl;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithTitle;

class UrlsSheet implements FromArray, WithTitle
{
    public function __construct(protected Collection $contacts)
    {
    }

    public function array(): array
    {
        return $this->contacts->map(function (Contact $contact) {
            return $contact->urls->map(function (ContactUrl $website) {
                return [
                    'contact_id' => $website->contact_id,
                    'name' => $website->name,
                    'url' => $website->url,
                ];
            });
        })->toArray();
    }

    public function title(): string
    {
        return 'Websiten';
    }

}
