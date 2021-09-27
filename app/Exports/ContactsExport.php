<?php

namespace App\Exports;

use App\Exports\Sheets\AddressesSheet;
use App\Exports\Sheets\ContactsSheet;
use App\Exports\Sheets\DatesSheet;
use App\Exports\Sheets\EmailsSheet;
use App\Exports\Sheets\NumbersSheet;
use App\Exports\Sheets\UrlsSheet;
use App\Models\ContactGroup;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;

class ContactsExport implements WithMultipleSheets, WithTitle
{
    use Exportable;

    public function __construct(
        protected Collection $contacts
    )
    {
        //
    }

    public function sheets(): array
    {
        return [
            new ContactsSheet($this->contacts),
            new AddressesSheet($this->contacts),
            new DatesSheet($this->contacts),
            new EmailsSheet($this->contacts),
            new NumbersSheet($this->contacts),
            new UrlsSheet($this->contacts)
        ];
    }

    public function title(): string
    {
        return 'Kontakt Export';
    }
}
