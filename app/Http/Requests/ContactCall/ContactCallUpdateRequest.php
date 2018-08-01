<?php

namespace App\Http\Requests\ContactCall;

use Illuminate\Foundation\Http\FormRequest;

class ContactCallUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit calls');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'note' => 'nullable',
            'called_at' => 'required|date_format:d.m.Y H:i'
        ];
    }
}
