<?php

namespace App\GraphQL\Types;

use App\Models\Address;
use App\Models\ContactCall;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CallType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Call',
        'description' => 'A call',
        'model' => ContactCall::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the call',
            ],
            'note' => [
                'type' => Type::string(),
                'description' => 'The note of the call',
            ],
            'called_at' => [
                'type' => Type::string(),
                'description' => 'The called at timestamp of the call',
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The creation timestamp of the call',
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The last updated timestamp of the call',
            ],
        ];
    }
}
