<?php

return [
    'created_contact' => 'hat einen Kontakt erstellt',
    'updated_contact' => 'hat einen Kontakt aktualisiert',
    'deleted_contact' => 'hat einen Kontakt gelöscht',

    'yes' => 'Ja',
    'no' => 'Nein',

    'summary' => [
        'created' => [
            'contact' => 'Kontakt erstellt',
            'number' => 'Telefonnummer hinzugefügt',
            'email' => 'E-Mail-Adresse hinzugefügt',
            'url' => 'Webseite hinzugefügt',
            'address' => 'Adresse hinzugefügt',
            'date' => 'Termin hinzugefügt',
        ],
        'updated' => [
            'contact' => 'Kontakt geändert',
            'number' => 'Telefonnummer geändert',
            'email' => 'E-Mail-Adresse geändert',
            'url' => 'Webseite geändert',
            'address' => 'Adresse geändert',
            'date' => 'Termin geändert',
        ],
        'deleted' => [
            'contact' => 'Kontakt gelöscht',
            'number' => 'Telefonnummer gelöscht',
            'email' => 'E-Mail-Adresse gelöscht',
            'url' => 'Webseite gelöscht',
            'address' => 'Adresse gelöscht',
            'date' => 'Termin gelöscht',
        ],
    ],

    // Field labels per subject. Contact fields fall back to duplicates.fields.
    'fields' => [
        'number' => ['name' => 'Bezeichnung', 'number' => 'Nummer'],
        'email' => ['name' => 'Bezeichnung', 'email' => 'E-Mail'],
        'url' => ['name' => 'Bezeichnung', 'url' => 'URL'],
        'address' => [
            'name' => 'Bezeichnung',
            'street' => 'Straße',
            'zip' => 'PLZ',
            'city' => 'Ort',
            'state' => 'Bundesland',
            'country_id' => 'Land',
        ],
        'date' => ['name' => 'Bezeichnung', 'date' => 'Datum', 'skip_year' => 'Ohne Jahr'],
    ],
];
