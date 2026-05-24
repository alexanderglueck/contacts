<?php

namespace App\Http\Requests\Contact;

use App\Rules\ValidIBANFormat;
use Illuminate\Foundation\Http\FormRequest;

class ContactUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit contacts');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'salutation' => 'required',
            'title' => 'present',
            'firstname' => 'required',
            'lastname' => 'required',
            'title_after' => 'present',
            'company' => 'present',
            'department' => 'present',
            'job' => 'present',
            'gender_id' => 'integer|exists:genders,id',
            'nickname' => 'present',
            // <input type="date"> always sends ISO; the legacy d.m.Y payload
            // path still has to validate too, so we accept both and let the
            // Contact model's parseDate() mutator normalize on the way in.
            'date_of_birth' => 'nullable|sometimes|date_format:Y-m-d,d.m.Y',
            'died_at' => 'nullable|sometimes|date_format:Y-m-d,d.m.Y',
            'nationality_id' => 'nullable|sometimes|exists:countries,id',
            'iban' => new ValidIBANFormat
        ];
    }
}
