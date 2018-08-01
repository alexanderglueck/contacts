<?php

namespace App\Http\Requests\ContactGroup;

use Illuminate\Foundation\Http\FormRequest;

class ContactGroupStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create contactGroups');
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
        $validator->sometimes('parent_id', 'exists:tenant.contact_groups,id', function ($input) {
            return strlen($input->parent_id) > 0;
        });
    }
}
