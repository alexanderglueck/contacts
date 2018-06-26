<?php

namespace App\Http\Requests\Announcement;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAnnouncement extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit announcements');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title' => 'required',
            'body' => 'required',
            'expired_at' => 'nullable|date_format:Y-m-d H:i:s|after_or_equal:now',
            'pinned_at' => 'nullable|date_format:Y-m-d H:i:s|before_or_equal:now'
        ];
    }
}
