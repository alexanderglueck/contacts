<?php

namespace App\Http\Requests;

use App\Models\Contact;
use Illuminate\Foundation\Http\FormRequest;

class MergeContactRequest extends FormRequest
{
    /**
     * Scalar fields the user picks side-for-side. team_id, user_id, ulid,
     * timestamps stay on the kept contact and aren't part of the merge form.
     */
    public const FIELDS = [
        'firstname', 'lastname', 'title', 'title_after', 'date_of_birth',
        'iban', 'salutation', 'gender_id', 'company', 'vatin', 'department',
        'job', 'custom_id', 'nickname', 'active', 'first_met', 'note',
        'died_at', 'died_from', 'nationality_id', 'image',
    ];

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kept_ulid' => ['required', 'string'],
            'loser_ulid' => ['required', 'string', 'different:kept_ulid'],
            'choices' => ['required', 'array'],
            'choices.*' => ['nullable', 'string', 'in:left,right'],
            // subResources[<relation>][<id>] = bool — default true (keep)
            'subResources' => ['nullable', 'array'],
            'subResources.*' => ['nullable', 'array'],
            'subResources.*.*' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Validate that for every field where at least one side has a non-empty
     * value, the user picked a side. Picking nothing while both sides are
     * empty is fine (the field remains empty).
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $kept = Contact::where('ulid', $this->input('kept_ulid'))->first();
            $loser = Contact::where('ulid', $this->input('loser_ulid'))->first();

            if (! $kept || ! $loser) {
                $v->errors()->add('kept_ulid', __('duplicates.errors.contact_not_found'));

                return;
            }

            // 'left' and 'right' in the UI map to whichever ULID the user
            // designated as kept vs loser; the controller treats them
            // identically by reading the choice + the two ULIDs.
            foreach (self::FIELDS as $field) {
                $left = $kept->{$field};
                $right = $loser->{$field};

                $leftFilled = $this->valueIsFilled($left);
                $rightFilled = $this->valueIsFilled($right);

                if (! $leftFilled && ! $rightFilled) {
                    continue; // both empty — no choice needed
                }

                if (! in_array($this->input("choices.{$field}"), ['left', 'right'], true)) {
                    $label = __('duplicates.fields.' . $field);
                    // __ returns the key string when no translation exists — fall back to the
                    // column name in that case rather than printing "duplicates.fields.firstname".
                    if ($label === 'duplicates.fields.' . $field) {
                        $label = $field;
                    }
                    $v->errors()->add("choices.{$field}", __('duplicates.errors.choice_required', ['field' => $label]));
                }
            }
        });
    }

    private function valueIsFilled($value): bool
    {
        if ($value === null) {
            return false;
        }
        if (is_string($value) && trim($value) === '') {
            return false;
        }

        return true;
    }
}
