<?php

namespace App\Http\Requests\ContactDate;

use Illuminate\Foundation\Http\FormRequest;

class ContactDateUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit dates');
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
            'date' => 'required|date_format:Y-m-d',
            'skip_year' => 'boolean',
        ];
    }
}
