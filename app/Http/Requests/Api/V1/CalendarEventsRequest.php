<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CalendarEventsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('view calendar');
    }

    public function rules(): array
    {
        return [
            // ISO 8601 date (no time). FullCalendar's web endpoint accepts the
            // same format — we standardise on date-only for the API so the
            // client doesn't have to think about timezones for all-day events.
            'from' => ['required', 'date_format:Y-m-d'],
            'to' => ['required', 'date_format:Y-m-d', 'after_or_equal:from'],
        ];
    }
}
