<?php

namespace App\Http\Requests\ContactDate;

use Illuminate\Foundation\Http\FormRequest;

class ContactDateStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create dates');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $attachYear = '';
        if ( ! $this->post('skip_year')) {
            $attachYear = 'Y';
        }

        return [
            'name' => 'required',
            'date' => 'required|date_format:d.m.' . $attachYear,
            'skip_year' => 'boolean'
        ];
    }
}
