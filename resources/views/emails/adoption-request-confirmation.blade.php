@component('mail::message')
{{ __('mail.adoption_request_confirmation.greeting', ['name' => $adoptionRequest->adopterProfile->first_name]) }}

{{ __('mail.adoption_request_confirmation.intro', ['animal' => $adoptionRequest->animal->name]) }}

@component('mail::button', ['url' => route('client.animal.show', $adoptionRequest->animal)])
{{ __('mail.adoption_request_confirmation.button', ['animal' => $adoptionRequest->animal->name]) }}
@endcomponent
@endcomponent