@component('mail::message')
{{ __('mail.new_adoption_request.intro', ['animal' => $adoptionRequest->animal->name]) }}

**{{ __('mail.new_adoption_request.from_label') }}:** {{ $adoptionRequest->adopterProfile->first_name }} {{ $adoptionRequest->adopterProfile->last_name }} ({{ $adoptionRequest->adopterProfile->email }})

**{{ __('mail.new_adoption_request.message_label') }}:**
{{ $adoptionRequest->content }}

@component('mail::button', ['url' => route('notifications.index')])
{{ __('mail.new_adoption_request.button') }}
@endcomponent
@endcomponent