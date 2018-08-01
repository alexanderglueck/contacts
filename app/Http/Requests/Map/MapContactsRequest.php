<?php

namespace App\Http\Requests\Map;

use Illuminate\Foundation\Http\FormRequest;

class MapContactsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('view map');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bounds' => 'required|string'
        ];
    }
}
