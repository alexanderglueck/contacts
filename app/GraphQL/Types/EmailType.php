<?php

namespace App\GraphQL\Types;

use App\Models\Address;
use App\Models\ContactCall;
use App\Models\ContactDate;
use App\Models\ContactEmail;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class EmailType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Email',
        'description' => 'An email',
        'model' => ContactEmail::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the email',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the email',
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'The email of the email',
            ],
            'slug' => [
                'type' => Type::string(),
                'description' => 'The slug of the email',
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The creation timestamp of the email',
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The last updated timestamp of the email',
            ],
        ];
    }
}
