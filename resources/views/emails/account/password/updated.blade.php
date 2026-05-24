@component('mail::message')
# {{ __('mail.password_updated.heading') }}

{{ __('mail.password_updated.body') }}

{{ __('mail.thanks') }},<br>
{{ config('app.name') }}
@endcomponent
