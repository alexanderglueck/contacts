<?php

namespace App\GraphQL\Types;

use App\Models\Comment;
use App\Models\Contact;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CommentType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Comment',
        'description' => 'A comment',
        'model' => Comment::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the contact',
            ],
            'comment' => [
                'type' => Type::string(),
                'description' => 'The lastname of the contact',
            ],
            'parent_id' => [
                'type' => Type::int(),
                'description' => 'The firstname of the contact',
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The full name of the contact. First and lastname',
                'selectable' => false,
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The title of the contact',
            ],

            // Relations
            'owner' => [
                'type' => GraphQL::type('user'),
                'description' => 'The author of this comment',
                'always' => ['name'],
            ]
        ];
    }
}
