<?php

namespace App\GraphQL\Types;

use App\Models\Gender;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class GenderType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Gender',
        'description' => 'A gender',
        'model' => Gender::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the gender',
            ],
            'gender' => [
                'type' => Type::string(),
                'description' => 'The gender of the gender',
            ],
        ];
    }
}
