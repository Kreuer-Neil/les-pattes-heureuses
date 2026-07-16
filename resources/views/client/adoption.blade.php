<x-layout.app>
    <x-client.navbar />
    <main class="flex flex-col items-center">
        <h1 class="sr-only">{{ config('app.name', 'Les pattes heureuses') . ' - ' . __('client.nav.animals') }}</h1>

        <section class="general-section items-center">
            <h2 class="title text-center">{{ __('client.animals.title') }}</h2>
            <p class="labor-text text-center">{{ __('client.animals.intro') }}</p>

            @if($animals->isEmpty())
                <p class="labor-text">{{ __('client.animals.empty') }}</p>
            @else
                <div class="grid gap-6 w-full max-w-[120rem] sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                    @foreach($animals as $animal)
                        <x-client.animal-card :animal="$animal" />
                    @endforeach
                </div>
            @endif
        </section>
    </main>
</x-layout.app>