<?php

namespace App\Http\Requests\Account;

use Illuminate\Foundation\Http\FormRequest;

class NotificationSettingUpdateRequest extends FormRequest
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
            'send_daily' => 'nullable|boolean',
            'send_weekly' => 'nullable|boolean',
        ];
    }
}
