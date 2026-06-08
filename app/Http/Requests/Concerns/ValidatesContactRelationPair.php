<?php

namespace App\Http\Requests\Concerns;

use App\Models\Contact;
use App\Models\ContactRelation;
use Illuminate\Validation\Validator;

/**
 * Shared store-time validation for a contact relation: rejects linking a
 * contact to itself and rejects a pair that already exists (in either
 * direction). Used by both the web and API store requests so the rule can't
 * drift between layers.
 */
trait ValidatesContactRelationPair
{
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $contact = $this->route('contact');
            $other = Contact::where('ulid', $this->input('related_contact'))->first();

            if (! $other) {
                return; // the exists rule already reported this
            }

            if ($other->id === $contact->id) {
                $validator->errors()->add('related_contact', trans('flash_message.contact_relation.self'));

                return;
            }

            // Single wrapping where() so the tenant scope's team_id predicate
            // ANDs across the whole OR group instead of leaking past it.
            $exists = ContactRelation::where(function ($query) use ($contact, $other) {
                $query->where(function ($pair) use ($contact, $other) {
                    $pair->where('contact_a_id', $contact->id)->where('contact_b_id', $other->id);
                })->orWhere(function ($pair) use ($contact, $other) {
                    $pair->where('contact_a_id', $other->id)->where('contact_b_id', $contact->id);
                });
            })->exists();

            if ($exists) {
                $validator->errors()->add('related_contact', trans('flash_message.contact_relation.duplicate'));
            }
        });
    }
}
