<?php

namespace App\GraphQL\Types;

use App\Models\GiftIdea;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class GiftIdeaType extends GraphQLType
{
    protected $attributes = [
        'name' => 'GiftIdea',
        'description' => 'A gift idea',
        'model' => GiftIdea::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the gift idea',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the gift idea',
            ],
            'description' => [
                'type' => Type::string(),
                'description' => 'The description of the gift idea',
            ],
            'slug' => [
                'type' => Type::string(),
                'description' => 'The slug of the gift idea',
            ],
            'due_at' => [
                'type' => Type::string(),
                'description' => 'The due timestamp of the gift idea',
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The creation timestamp of the gift idea',
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The last updated timestamp of the gift idea',
            ],
        ];
    }
}
