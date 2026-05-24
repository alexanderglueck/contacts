<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class ContactImageUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('edit contacts');
    }

    public function rules(): array
    {
        return [
            // Cap at 8MB so phones can upload originals without exhausting
            // PHP's memory_limit during the Intervention fit() step. The
            // server downsizes to 400x400 regardless of input dimensions.
            'file' => ['required', 'file', 'mimes:jpeg,png', 'max:8192'],
        ];
    }
}
