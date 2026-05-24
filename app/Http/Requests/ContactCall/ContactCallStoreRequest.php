<?php

namespace App\Http\Requests\ContactCall;

use Illuminate\Foundation\Http\FormRequest;

class ContactCallStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create calls');
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
            // <input type="datetime-local"> sends Y-m-d\TH:i (with or without
            // seconds depending on browser); also accept the legacy d.m.Y H:i
            // payload so historical entries still validate.
            'called_at' => 'required|date_format:Y-m-d\TH:i,Y-m-d\TH:i:s,d.m.Y H:i',
        ];
    }
}
