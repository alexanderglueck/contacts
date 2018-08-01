<?php

namespace App\Http\Requests\GiftIdea;

use Illuminate\Foundation\Http\FormRequest;

class GiftIdeaUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('edit giftIdeas');
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
            'due_at' => 'nullable|sometimes|date_format:d.m.Y',
            'description' => 'nullable',
            'url' => 'nullable|sometimes|url',
        ];
    }
}
