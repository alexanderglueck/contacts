<?php

namespace App\Imports\Sheets;

use App\Imports\MappingHolder;
use App\Models\Contact;
use App\Models\Gender;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;

class ContactsSheet implements ToCollection
{
    public function __construct(
        protected int           $contactGroupId,
        protected MappingHolder $mappingHolder
    )
    {
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $contact = new Contact([
                'lastname' => $row[1],
                'firstname' => $row[2],
                'company' => $row[3],
                'job' => $row[4],
                'department' => $row[5],
                'title' => $row[6],
                'title_after' => $row[7],
                'salutation' => $row[8],
                'nickname' => $row[9],
                'image' => $row[10],
            ]);
            $contact->gender_id = Gender::where('gender', '=', $row[9])->first()->id;

            $contact->created_by = Auth::id();
            $contact->updated_by = Auth::id();

            if ($contact->save()) {
                // saved

                $importId = $contact->id;

                $this->mappingHolder->mapping[$row[0]] = $importId;

                $contact->contactGroups()->attach($importId, [
                    'contact_group_id' => $this->contactGroupId
                ]);
            }
        }
    }
}
