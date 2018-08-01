<?php

namespace App\Http\Requests\ContactNumber;

use Illuminate\Foundation\Http\FormRequest;

class ContactNumberUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit numbers');
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
            'number' => 'required|regex:/^[0-9\s \-()+]*$/'
        ];
    }
}
