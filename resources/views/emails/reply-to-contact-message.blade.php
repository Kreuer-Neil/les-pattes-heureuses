@component('mail::message')
{{ __('mail.reply_to_contact_message.greeting', ['name' => $contactMessage->first_name]) }}

{{ $message }}

{{ $signature }}

---

{{ __('mail.reply_to_contact_message.original_message_intro') }}

> {{ $contactMessage->content }}
@endcomponent