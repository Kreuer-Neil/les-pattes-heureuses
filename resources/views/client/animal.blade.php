@php($isAvailable = $animal->status?->name === \App\Enums\Animals\Status::Available->value)

<x-layout.app>
    <x-client.navbar />
    <main class="flex flex-col items-center">
        <h1 class="sr-only">{{ $animal->name }}</h1>

        <article class="general-section items-center lg:flex-row lg:items-start gap-10">
            <img src="{{ $animal->image ? asset('storage/' . $animal->image) : asset('images/cat_dog.jpg') }}"
                 alt="{{ $animal->name }}"
                 class="rounded-lg w-full lg:w-2/5 aspect-[4/3] object-cover">

            <div class="flex flex-col gap-4 max-w-2xl">
                <a href="{{ route('client.animals') }}" class="underline">&larr; {{ __('client.animal.back_to_list') }}</a>

                <div class="flex items-center gap-3">
                    <h2 class="title">{{ $animal->name }}</h2>
                    @if($animal->status)
                        <span class="status-tag">{{ __('client.animal.statuses.' . $animal->status->name) }}</span>
                    @endif
                </div>
                <p class="subtitle">{{ $animal->breed?->label ?? __('client.species.' . $animal->specie?->name) }}</p>

                <p class="labor-text">{{ $animal->personality }}</p>

                <dl class="grid grid-cols-2 gap-x-6 gap-y-2 text-xl">
                    <dt class="font-medium">{{ __('client.animal.gender') }}</dt>
                    <dd>{{ __('client.animal.genders.' . $animal->gender) }}</dd>

                    <dt class="font-medium">{{ __('client.animal.age') }}</dt>
                    <dd>{{ trans_choice('client.animal.age_years', $animal->born_at->age, ['count' => $animal->born_at->age]) }}</dd>
                </dl>

                @if($isAvailable)
                    <button type="button" command="show-modal" commandfor="adoption-request-dialog" class="custom-btn w-fit">
                        {{ __('client.animal.request_visit') }}
                    </button>
                @endif
            </div>
        </article>
    </main>

    @if($isAvailable)
        <x-client.dialog id="adoption-request-dialog" :title="__('client.animal.request_visit')">
            <form class="form" method="POST" action="{{ route('client.adoption.request', $animal) }}">
                @csrf
                <p class="labor-text">{{ __('client.animal.request_text', ['name' => $animal->name]) }}</p>
                <fieldset>
                    <div class="w-full flex flex-col sm:flex-row gap-4">
                        <x-form.input name="last_name" class="grow" />
                        <x-form.input name="first_name" class="grow" />
                    </div>
                    <x-form.input name="email" />
                    <x-form.input name="message" type="textarea" />
                </fieldset>
                <button type="submit" class="custom-btn">{{ __('client.animal.send_request') }}</button>
            </form>
        </x-client.dialog>
    @endif
</x-layout.app>