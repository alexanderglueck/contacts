<?php

namespace App\Http\Requests\ContactAddress;

use Illuminate\Foundation\Http\FormRequest;

class ContactAddressStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create addresses');
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
            'country_id' => 'required|exists:system.countries,id',
            'latitude' => 'sometimes|required|numeric',
            'longitude' => 'sometimes|required|numeric'
        ];
    }
}
