<?php

namespace App\Http\Requests\ContactRelation;

use App\Http\Requests\Concerns\ValidatesContactRelationPair;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ContactRelationStoreRequest extends FormRequest
{
    use ValidatesContactRelationPair;

    public function authorize(): bool
    {
        return $this->user()->can('create relations');
    }

    public function rules(): array
    {
        return [
            // Tenant-scoped existence: the contact must live in the current
            // team. The BelongsToTenantScope doesn't reach the validator, so
            // the team_id predicate is spelled out here.
            'related_contact' => [
                'required',
                'string',
                Rule::exists('contacts', 'ulid')->where('team_id', $this->user()->current_team_id),
            ],
            'forward_label' => ['required', 'string', 'max:255'],
            'inverse_label' => ['nullable', 'string', 'max:255'],
        ];
    }
}
