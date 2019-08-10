<?php

namespace App\GraphQL\Types;

use App\Models\Address;
use App\Models\ContactCall;
use App\Models\ContactDate;
use App\Models\ContactEmail;
use App\Models\ContactNote;
use App\Models\ContactNumber;
use App\Models\ContactUrl;
use App\Models\Country;
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
