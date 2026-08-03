@component('mail::message')
{{ __('mail.new_contact_message.intro') }}

**{{ __('mail.new_contact_message.from_label') }}:** {{ $contactMessage->first_name }} {{ $contactMessage->last_name }} ({{ $contactMessage->email }})
**{{ __('mail.new_contact_message.type_label') }}:** {{ __('mail.new_contact_message.types.'.$contactMessage->type->value) }}

**{{ __('mail.new_contact_message.message_label') }}:**
{{ $contactMessage->content }}

@component('mail::button', ['url' => route('notifications.index')])
{{ __('mail.new_contact_message.button') }}
@endcomponent
@endcomponent