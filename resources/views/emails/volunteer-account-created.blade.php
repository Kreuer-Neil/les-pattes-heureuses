@component('mail::message')
{{ __('mail.volunteer_account.greeting', ['name' => $user->name]) }}

{{ $isNewAccount ? __('mail.volunteer_account.intro_new') : __('mail.volunteer_account.intro_reissued') }}

{{ __('mail.volunteer_account.credentials_intro') }}

**{{ __('mail.volunteer_account.email_label') }}:** {{ $user->email }}
**{{ __('mail.volunteer_account.password_label') }}:** {{ $temporaryPassword }}

{{ __('mail.volunteer_account.must_change_password') }}

@component('mail::button', ['url' => route('login')])
{{ __('mail.volunteer_account.login_button') }}
@endcomponent

{{ __('mail.volunteer_account.footer') }}
@endcomponent