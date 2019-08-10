<?php

namespace App\GraphQL\Types;

use App\Models\Address;
use App\Models\ContactCall;
use App\Models\ContactDate;
use App\Models\ContactEmail;
use App\Models\ContactNote;
use App\Models\ContactNumber;
use App\Models\ContactUrl;
use App\Models\Country;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CountryType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Country',
        'description' => 'A country',
        'model' => Country::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the country',
            ],
            'country' => [
                'type' => Type::string(),
                'description' => 'The name of the country',
            ],
            'code' => [
                'type' => Type::string(),
                'description' => 'The country code of the country',
            ]
        ];
    }
}
