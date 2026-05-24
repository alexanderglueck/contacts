<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Note: latitude/longitude are NOT accepted from the client. They're
 * populated asynchronously by ContactAddressObserver → GeocodeContactAddress
 * job hitting Nominatim, using the textual address fields as input.
 */
class StoreContactAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create addresses');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'street' => ['required', 'string', 'max:255'],
            'zip' => ['required', 'string', 'max:32'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['sometimes', 'nullable', 'string', 'max:255'],
            'country_id' => ['required', 'integer', 'exists:countries,id'],
        ];
    }
}
