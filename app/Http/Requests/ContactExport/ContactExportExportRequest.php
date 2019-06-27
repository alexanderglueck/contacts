<?php

namespace App\Http\Requests\ContactExport;

use Illuminate\Foundation\Http\FormRequest;

class ContactExportExportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create export');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'contact_group_id' => 'required|integer|exists:contact_groups,id',
        ];
    }
}
