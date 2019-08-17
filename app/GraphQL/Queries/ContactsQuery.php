<?php

namespace App\GraphQL\Queries;

use Closure;
use App\Models\Contact;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\Facades\GraphQL;

class ContactsQuery extends Query
{
    protected $attributes = [
        'name' => 'Contacts query'
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('contact'));
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int()],
            'email' => ['name' => 'email', 'type' => Type::string()]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if (isset($args['id'])) {
            return Contact::where('id', $args['id'])->get();
        }

        return Contact::all();
    }
}
