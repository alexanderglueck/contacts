<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class TenantSelectionStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'team' => [
                'required',
                'integer',
                'exists:teams,id',
                Rule::exists('team_user', 'team_id')->where(function ($query) {
                    $query->where('user_id', auth()->id());
                })
            ]
        ];
    }
}
