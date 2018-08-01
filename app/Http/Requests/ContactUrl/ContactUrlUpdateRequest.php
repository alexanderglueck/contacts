<?php

namespace App\Http\Requests\ContactUrl;

use Illuminate\Foundation\Http\FormRequest;

class ContactUrlUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit urls');
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
            'url' => 'required|url'
        ];
    }
}
