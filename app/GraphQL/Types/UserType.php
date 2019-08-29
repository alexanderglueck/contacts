<?php

namespace App\GraphQL\Types;

use App\Models\User;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UserType extends GraphQLType
{
    protected $attributes = [
        'name' => 'User',
        'description' => 'A user',
        'model' => User::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the user'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the user',
            ],
            'slug' => [
                'type' => Type::string(),
                'description' => 'The slug of the user',
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'The email of the user',
            ],
            'image' => [
                'type' => Type::string(),
                'description' => 'The path to the profile image of the user',
            ],
            'api_token' => [
                'type' => Type::string(),
                'description' => 'The api token of the user',
                'privacy' => function (array $args): bool {
                    return $args['id'] == Auth::id();
                }
            ]
        ];
    }
}
