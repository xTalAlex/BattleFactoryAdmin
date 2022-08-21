@component('mail::message')
# @lang('Hello :username!', [ 'username' => $username ])

Your {{ config('app.name') }} account has been created.

@if($password)
@component('mail::panel')
@lang('This is your generated password'): {{ $password }} 
@endcomponent
@endif

@component('mail::button', ['url' => $url])
@lang('To Website')
@endcomponent

@lang('Thanks'),<br>
{{ config('app.name') }}
@endcomponent
