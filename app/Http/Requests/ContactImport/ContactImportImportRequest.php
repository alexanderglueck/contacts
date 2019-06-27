<?php

namespace App\Http\Requests\ContactImport;

use Illuminate\Foundation\Http\FormRequest;

class ContactImportImportRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create import');
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
            'import_file' => 'required|file|mimes:xlsx'
        ];
    }
}
