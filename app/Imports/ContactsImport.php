<?php

namespace App\Imports;

use App\Imports\Sheets\AddressesSheet;
use App\Imports\Sheets\ContactsSheet;
use App\Imports\Sheets\DatesSheet;
use App\Imports\Sheets\EmailsSheet;
use App\Imports\Sheets\NumbersSheet;
use App\Imports\Sheets\UrlsSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ContactsImport implements WithMultipleSheets
{
    protected int $contactGroupId;

    public function __construct(
        protected MappingHolder $mappingHolder
    )
    {
    }

    public function sheets(): array
    {
        return [
            new ContactsSheet($this->contactGroupId, $this->mappingHolder),
            new AddressesSheet($this->mappingHolder),
            new DatesSheet($this->mappingHolder),
            new EmailsSheet($this->mappingHolder),
            new NumbersSheet($this->mappingHolder),
            new UrlsSheet($this->mappingHolder)
        ];
    }

    public function setContactGroup(int $contactGroupId): void
    {
        $this->contactGroupId = $contactGroupId;
    }
}
