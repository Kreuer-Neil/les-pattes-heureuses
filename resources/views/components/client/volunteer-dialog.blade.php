<x-client.dialog id="volunteer-dialog" :title="__('client.contact.volunteer')">
    <form class="form" method="POST" action="{{ route('client.contact') }}">
        @csrf
        <input type="hidden" name="type" value="volunteer">
        <p class="labor-text">{!! __('client.contact.volunteer_text') !!}</p>
        <fieldset>
            <div class="w-full flex flex-col sm:flex-row gap-4">
                <x-form.input name="last_name" class="grow" />
                <x-form.input name="first_name" class="grow" />
            </div>
            <x-form.input name="email" />
            <x-form.input name="message" type="textarea" />
        </fieldset>
        <button type="submit" class="custom-btn">{{ __('client.contact.send') }}</button>
    </form>
</x-client.dialog>