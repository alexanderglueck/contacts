<?php

namespace App\GraphQL\Types;

use App\Models\ContactNote;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class NoteType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Note',
        'description' => 'A note',
        'model' => ContactNote::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the note',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the note',
            ],
            'note' => [
                'type' => Type::string(),
                'description' => 'The note of the note',
            ],
            'slug' => [
                'type' => Type::string(),
                'description' => 'The slug of the note',
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The creation timestamp of the note',
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The last updated timestamp of the note',
            ],
        ];
    }
}
