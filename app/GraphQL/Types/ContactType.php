<?php

namespace App\GraphQL\Types;

use App\Models\Contact;
use Closure;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Type as GraphQLType;

class ContactType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Contact',
        'description' => 'A contact',
        'model' => Contact::class,
    ];

    public function fields(): array
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the contact',
            ],
            'lastname' => [
                'type' => Type::string(),
                'description' => 'The lastname of the contact',
            ],
            'firstname' => [
                'type' => Type::string(),
                'description' => 'The firstname of the contact',
            ],
            'fullname' => [
                'type' => Type::string(),
                'description' => 'The full name of the contact. First and lastname',
                'selectable' => false,
            ],
            'title' => [
                'type' => Type::string(),
                'description' => 'The title of the contact',
            ],
            'titel_after' => [
                'type' => Type::string(),
                'description' => 'The title after of the contact',
            ],
            'salutation' => [
                'type' => Type::string(),
                'description' => 'The salutation of the contact',
            ],
            'company' => [
                'type' => Type::string(),
                'description' => 'The company of the contact',
            ],
            'department' => [
                'type' => Type::string(),
                'description' => 'The department of the contact',
            ],
            'job' => [
                'type' => Type::string(),
                'description' => 'The job of the contact',
            ],
            'nickname' => [
                'type' => Type::string(),
                'description' => 'The nickname of the contact',
            ],
            'slug' => [
                'type' => Type::string(),
                'description' => 'The slug of the contact',
            ],

            // Relations
            'gender' => [
                'type' => GraphQL::type('gender'),
                'description' => 'The gender of the contact'
            ],

            'comments' => [
                'type' => Type::listOf(GraphQL::type('comment')),
                'description' => 'The comments on this contacts ',
                'always' => ['body'],
            ],

            'addresses' => [
                'type' => Type::listOf(GraphQL::type('address')),
                'description' => 'The addresses on this contacts '
            ],

            'calls' => [
                'type' => Type::listOf(GraphQL::type('call')),
                'description' => 'The calls on this contacts '
            ],

            'dates' => [
                'type' => Type::listOf(GraphQL::type('date')),
                'description' => 'The dates on this contacts '
            ],

            'emails' => [
                'type' => Type::listOf(GraphQL::type('email')),
                'description' => 'The emails on this contacts '
            ],

            'notes' => [
                'type' => Type::listOf(GraphQL::type('note')),
                'description' => 'The notes on this contacts '
            ],

            'numbers' => [
                'type' => Type::listOf(GraphQL::type('number')),
                'description' => 'The numbers on this contacts '
            ],

            'urls' => [
                'type' => Type::listOf(GraphQL::type('url')),
                'description' => 'The urls on this contacts '
            ],

            'gift_ideas' => [
                'type' => Type::listOf(GraphQL::type('gift_idea')),
                'description' => 'The gift ideas on this contacts '
            ],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info, Closure $getSelectFields)
    {
        // $info->getFieldSelection($depth = 3);

        $fields = $getSelectFields();
        $select = $fields->getSelect();
        $with = $fields->getRelations();

        $contacts = Contact::select($select)->with($with);

        return $contacts->get();
    }
}
