<?php

namespace App\GraphQL\Types;

use App\Models\Address;
use App\Models\ContactAddress;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class AddressType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Address',
        'description' => 'An address',
        'model' => ContactAddress::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the address',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the address',
            ],
            'street' => [
                'type' => Type::string(),
                'description' => 'The street of the address',
            ],
            'zip' => [
                'type' => Type::string(),
                'description' => 'The zip code of the address',
            ],
            'city' => [
                'type' => Type::string(),
                'description' => 'The city of the address',
            ],
            'country_id' => [
                'type' => Type::int(),
                'description' => 'The country id of the address',
            ],
            'latitude' => [
                'type' => Type::float(),
                'description' => 'The latitude of the address',
            ],
            'longitude' => [
                'type' => Type::float(),
                'description' => 'The longitude of the address',
            ],
            'slug' => [
                'type' => Type::string(),
                'description' => 'The slug of the address',
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The creation timestamp of the address',
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The last updated timestamp of the address',
            ],

            // relations
            'country' => [
                'type' => GraphQL::type('country'),
                'description' => 'The country of the address'
            ]
        ];
    }
}
