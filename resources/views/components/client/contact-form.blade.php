<article class="general-section bg-background-1" id="contact">
    <div class="items-select">
        <span tabindex="0" class="items-select-item items-selected">{!! __('client.contact.title') !!}</span>
        <span tabindex="0" class="items-select-item">{!! __('client.contact.volunteer') !!}</span>
    </div>
    <form class="form" id="contact-form">
        <h2 class="title self-center">{!! __('client.contact.title') !!}</h2>
        <p class="labor-text">{!! __('client.contact.text') !!}</p>
        <fieldset>
            <div class="w-full flex flex-col sm:flex-row gap-4">
                <x-form.input name="last_name" class="grow" />
                <x-form.input name="first_name" class="grow" />
            </div>
            <x-form.input name="email" />
            <x-form.input name="message" type="textarea" />
        </fieldset>

    </form>

    <form class="form hidden" id="volunteer-form"
    method="POST" action="{{ route('client.contact') }}">
        <h2 class="title">{!! __('client.contact.volunteer') !!}</h2>
        <p class="labor-text">{!! __('client.contact.volunteer_text') !!}</p>

        <fieldset>
            @csrf
            <div class="w-full flex flex-col sm:flex-row gap-4">
                <x-form.input name="last_name" class="grow" />
                <x-form.input name="first_name" class="grow" />
            </div>
            <x-form.input name="email" />
            <x-form.input name="message" type="textarea" />
        </fieldset>
    </form>
</article>
