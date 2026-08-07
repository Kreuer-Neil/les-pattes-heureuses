@component('mail::message')
{{ __('mail.reply_to_adoption_request.greeting', ['name' => $adoptionRequest->adopterProfile->first_name]) }}

{{ $message }}

{{ $signature }}

---

{{ __('mail.reply_to_adoption_request.original_message_intro', ['animal' => $adoptionRequest->animal->name]) }}

> {{ $adoptionRequest->content }}
@endcomponent