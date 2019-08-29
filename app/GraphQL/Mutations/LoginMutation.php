<?php

namespace App\GraphQL\Mutations;

use CLosure;
use GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\ResolveInfo;

class LoginMutation extends Mutation
{
    protected $attributes = [
        'name' => 'Login'
    ];

    public function type(): Type
    {
        return GraphQL::type('user');
    }

    public function args(): array
    {
        return [
            'email' => ['name' => 'email', 'type' => Type::nonNull(Type::string())],
            'password' => ['name' => 'password', 'type' => Type::nonNull(Type::string())]
        ];
    }

    protected function rules(array $args = []): array
    {
        return [
            'password' => ['required'],
            'email' => ['required', 'email']
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $resolveInfo, Closure $getSelectFields)
    {
        if ( ! auth()->attempt($args)) {
            return;
        }

        return auth()->user();
    }
}
