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
            // 8 MB cap on the raw upload (8192 KB — that's how Laravel's
            // file `max` rule reads its argument). Phones routinely produce
            // 5–8 MB photos out of camera, especially with HEIC capture.
            // The server resizes to 400×400 and re-encodes to JPEG before
            // storing, so this only protects PHP's memory_limit during
            // the Imagick decode step.
            //
            // Accepted formats:
            //   jpeg, png, webp  — decoded by GD (always available)
            //   heic, heif, avif — decoded by Imagick + libheif (already
            //                      compiled into the container image)
            // On-disk output is always .jpg regardless of input so the
            // same file renders on the web (no HEIC support in browsers)
            // and on the Android dialer without extra decoding.
            'file' => ['required', 'file', 'mimes:jpeg,png,webp,heic,heif,avif', 'max:8192'],
        ];
    }
}
