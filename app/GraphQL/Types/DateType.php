<?php

namespace App\GraphQL\Types;

use App\Models\Address;
use App\Models\ContactCall;
use App\Models\ContactDate;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class DateType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Date',
        'description' => 'A date',
        'model' => ContactDate::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the date',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The note of the date',
            ],
            'date' => [
                'type' => Type::string(),
                'description' => 'The date of the date',
            ],
            'slug' => [
                'type' => Type::string(),
                'description' => 'The date of the date',
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The creation timestamp of the date',
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The last updated timestamp of the date',
            ],
        ];
    }
}
