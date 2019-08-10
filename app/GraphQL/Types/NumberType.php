<?php

namespace App\GraphQL\Types;

use App\Models\Address;
use App\Models\ContactCall;
use App\Models\ContactDate;
use App\Models\ContactEmail;
use App\Models\ContactNote;
use App\Models\ContactNumber;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class NumberType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Number',
        'description' => 'A number',
        'model' => ContactNumber::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the number',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the number',
            ],
            'number' => [
                'type' => Type::string(),
                'description' => 'The number of the number',
            ],
            'slug' => [
                'type' => Type::string(),
                'description' => 'The slug of the number',
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The creation timestamp of the number',
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The last updated timestamp of the number',
            ],
        ];
    }
}
