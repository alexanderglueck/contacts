@component('mail::message')
# {{ __('mail.activation.heading') }}

@component('mail::button', ['url' => route('activation.activate', $token)])
{{ __('mail.activation.action') }}
@endcomponent

{{ __('mail.thanks') }},<br>
{{ config('app.name') }}
@endcomponent
