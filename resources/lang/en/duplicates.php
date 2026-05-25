<?php

return [
    'merged' => 'Contacts merged successfully.',

    'errors' => [
        'same_contact' => 'Pick two different contacts.',
        'contact_not_found' => 'One of the contacts could not be found.',
        'choice_required' => 'Pick a side for :field.',
    ],

    // Human-readable labels for the scalar Contact columns that participate in
    // the merge. Used by the Compare page and by the validator's error string.
    'fields' => [
        'firstname' => 'First name',
        'lastname' => 'Last name',
        'title' => 'Title',
        'title_after' => 'Title (after name)',
        'date_of_birth' => 'Date of birth',
        'iban' => 'IBAN',
        'salutation' => 'Salutation',
        'gender_id' => 'Gender',
        'company' => 'Company',
        'vatin' => 'VAT number',
        'department' => 'Department',
        'job' => 'Job',
        'custom_id' => 'Custom ID',
        'nickname' => 'Nickname',
        'active' => 'Active',
        'first_met' => 'First met',
        'note' => 'Note',
        'died_at' => 'Died',
        'died_from' => 'Cause of death',
        'nationality_id' => 'Nationality',
        'image' => 'Profile image',
    ],
];
