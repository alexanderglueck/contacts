<?php

namespace App\Http\Requests\Api\V1;

use App\Http\Requests\Concerns\ValidatesContactRelationPair;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreContactRelationRequest extends FormRequest
{
    use ValidatesContactRelationPair;

    public function authorize(): bool
    {
        return $this->user()->can('create relations');
    }

    public function rules(): array
    {
        return [
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
