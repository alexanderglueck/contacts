<?php

namespace App\Http\Requests\ContactAddress;

use Illuminate\Foundation\Http\FormRequest;

class ContactAddressUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit addresses');
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
            'street' => 'required',
            'zip' => 'required',
            'city' => 'required',
            'state' => 'required',
            'country_id' => 'required|exists:countries,id',
            'latitude' => 'sometimes|required|numeric',
            'longitude' => 'sometimes|required|numeric'
        ];
    }
}
