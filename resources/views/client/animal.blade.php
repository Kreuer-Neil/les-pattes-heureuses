@php($isAvailable = $animal->status?->name === \App\Enums\Animals\Status::Available->value)

<x-layout.app>
    <x-client.navbar />
    <main class="flex flex-col items-center">
        <h1 class="sr-only">{{ $animal->name }}</h1>

        <article class="general-section items-center lg:flex-row lg:items-start gap-10">
            <x-client.animal-image :animal="$animal" class="rounded-lg w-full lg:w-2/5 aspect-[4/3] object-cover"
                sizes="(min-width: 1024px) 40vw, 100vw" />

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

                <div class="flex flex-wrap gap-3">
                    @if($isAvailable)
                        <button type="button" command="show-modal" commandfor="adoption-request-dialog" class="custom-btn w-fit">
                            {{ __('client.animal.request_visit') }}
                        </button>
                    @endif
                    <button type="button" command="show-modal" commandfor="share-dialog" class="custom-btn w-fit">
                        {{ __('client.animal.share.button') }}
                    </button>
                </div>
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

    <x-client.dialog id="share-dialog" :title="__('client.animal.share.title')">
        <div class="form">
            <p class="labor-text">{{ __('client.animal.share.text') }}</p>
            <div class="w-full flex gap-2">
                <input id="share-url-input" type="text" class="input grow" readonly
                       value="{{ route('client.animal.show', $animal) }}"
                       aria-label="{{ __('client.animal.share.title') }}">
                <button type="button" id="share-copy-btn" class="custom-btn w-fit">{{ __('client.animal.share.copy') }}</button>
            </div>
            <p id="share-copy-feedback" class="labor-text" role="status" hidden>{{ __('client.animal.share.copied') }}</p>
        </div>
    </x-client.dialog>

    <script>
        (function () {
            const dialog = document.getElementById('share-dialog');
            const input = document.getElementById('share-url-input');
            const copyBtn = document.getElementById('share-copy-btn');
            const feedback = document.getElementById('share-copy-feedback');

            input?.addEventListener('click', () => input.select());

            copyBtn?.addEventListener('click', async () => {
                input.select();
                await navigator.clipboard.writeText(input.value);
                feedback.hidden = false;
            });

            dialog?.addEventListener('close', () => {
                feedback.hidden = true;
            });
        })();
    </script>
</x-layout.app>