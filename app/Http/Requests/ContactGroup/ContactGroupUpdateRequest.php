<?php

namespace App\Http\Requests\ContactGroup;

use Illuminate\Foundation\Http\FormRequest;

class ContactGroupUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit contactGroups');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator $validator
     *
     * @return void
     */
    public function withValidator($validator)
    {
        $contactGroup = $this->route('contactGroup');

        $validator->sometimes('parent_id', 'exists:contact_groups,id|not_in:' . $contactGroup->id, function ($input) {
            return strlen($input->parent_id) > 0;
        });
    }
}
