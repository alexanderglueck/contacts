<?php

namespace App\GraphQL\Types;

use App\Models\Address;
use App\Models\ContactCall;
use App\Models\ContactDate;
use App\Models\ContactEmail;
use App\Models\ContactNote;
use App\Models\ContactNumber;
use App\Models\ContactUrl;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UrlType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Url',
        'description' => 'An url',
        'model' => ContactUrl::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the url',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of the url',
            ],
            'url' => [
                'type' => Type::string(),
                'description' => 'The url of the url',
            ],
            'slug' => [
                'type' => Type::string(),
                'description' => 'The slug of the url',
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The creation timestamp of the url',
            ],
            'updated_at' => [
                'type' => Type::string(),
                'description' => 'The last updated timestamp of the url',
            ],
        ];
    }
}
