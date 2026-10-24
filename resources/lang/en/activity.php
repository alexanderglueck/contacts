<?php

return [
    'created_contact' => 'created a contact',
    'updated_contact' => 'updated a contact',
    'deleted_contact' => 'deleted a contact',

    'yes' => 'Yes',
    'no' => 'No',

    'summary' => [
        'created' => [
            'contact' => 'Contact created',
            'number' => 'Phone number added',
            'email' => 'Email address added',
            'url' => 'Website added',
            'address' => 'Address added',
            'date' => 'Date added',
        ],
        'updated' => [
            'contact' => 'Contact updated',
            'number' => 'Phone number updated',
            'email' => 'Email address updated',
            'url' => 'Website updated',
            'address' => 'Address updated',
            'date' => 'Date updated',
        ],
        'deleted' => [
            'contact' => 'Contact deleted',
            'number' => 'Phone number deleted',
            'email' => 'Email address deleted',
            'url' => 'Website deleted',
            'address' => 'Address deleted',
            'date' => 'Date deleted',
        ],
    ],

    // Field labels per subject. Contact fields fall back to duplicates.fields.
    'fields' => [
        'number' => ['name' => 'Label', 'number' => 'Number'],
        'email' => ['name' => 'Label', 'email' => 'Email'],
        'url' => ['name' => 'Label', 'url' => 'URL'],
        'address' => [
            'name' => 'Label',
            'street' => 'Street',
            'zip' => 'Postcode',
            'city' => 'City',
            'state' => 'State',
            'country_id' => 'Country',
        ],
        'date' => ['name' => 'Label', 'date' => 'Date', 'skip_year' => 'Without year'],
    ],
];
