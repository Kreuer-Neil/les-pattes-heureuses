<x-layout.app>
    <x-client.navbar />
    <main class="flex flex-col items-center">
        <h1 class="sr-only">{{ config('app.name', 'Les pattes heureuses') . ' - ' . __('client.nav.animals') }}</h1>

        <section class="general-section items-center">
            <h2 class="title text-center">{{ __('client.animals.title') }}</h2>
            <p class="labor-text text-center">{{ __('client.animals.intro') }}</p>

            <form method="GET" action="{{ route('client.animals') }}" class="w-full max-w-[70rem] flex flex-col gap-4">
                <div class="form-item">
                    <label class="label" for="search-q">{{ __('client.animals.search.q') }}</label>
                    <input type="text" id="search-q" name="q" value="{{ request('q') }}"
                        placeholder="{{ __('client.animals.search.q_placeholder') }}" class="input">
                </div>

                <div class="flex flex-wrap gap-4 items-end">
                    <div class="form-item grow basis-40">
                        <label class="label" for="search-specie">{{ __('client.animals.search.specie') }}</label>
                        <input type="text" id="search-specie" name="specie" value="{{ request('specie') }}"
                            list="species-options" class="input w-full">
                    </div>

                    <div class="form-item grow basis-40">
                        <label class="label" for="search-breed">{{ __('client.animals.search.breed') }}</label>
                        <input type="text" id="search-breed" name="breed" value="{{ request('breed') }}"
                            list="breeds-options" class="input w-full">
                    </div>

                    <div class="form-item grow basis-40">
                        <label class="label" for="search-color">{{ __('client.animals.search.color') }}</label>
                        <input type="text" id="search-color" name="color" value="{{ request('color') }}"
                            list="colors-options" class="input w-full">
                    </div>

                    <div class="form-item grow basis-40">
                        <label class="label" for="search-gender">{{ __('client.animals.search.gender') }}</label>
                        <input type="text" id="search-gender" name="gender" value="{{ request('gender') }}"
                            list="genders-options" class="input w-full">
                    </div>

                    <div class="form-item grow basis-40">
                        <label class="label" for="search-age">{{ __('client.animals.search.age') }}</label>
                        <input type="number" min="0" id="search-age" name="age" value="{{ request('age') }}" class="input w-full">
                    </div>
                </div>

                <datalist id="species-options">
                    @foreach($speciesOptions as $option)
                        <option value="{{ $option }}"></option>
                    @endforeach
                </datalist>
                <datalist id="breeds-options">
                    @foreach($breedsOptions as $option)
                        <option value="{{ $option }}"></option>
                    @endforeach
                </datalist>
                <datalist id="colors-options">
                    @foreach($colorsOptions as $option)
                        <option value="{{ $option }}"></option>
                    @endforeach
                </datalist>
                <datalist id="genders-options">
                    @foreach($gendersOptions as $option)
                        <option value="{{ $option }}"></option>
                    @endforeach
                </datalist>

                <div class="flex gap-4 items-center">
                    <button type="submit" class="custom-btn w-fit">{{ __('client.animals.search.submit') }}</button>
                    @if($hasCriteria)
                        <a href="{{ route('client.animals') }}" class="underline">{{ __('client.animals.search.reset') }}</a>
                    @endif
                </div>
            </form>

            @if($exactAnimals->isEmpty() && $closeAnimals->isEmpty())
                <p class="labor-text">{{ $hasCriteria ? __('client.animals.search.no_results') : __('client.animals.empty') }}</p>
            @else
                @if($exactAnimals->isNotEmpty())
                    <div class="grid gap-6 w-full max-w-[120rem] sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach($exactAnimals as $animal)
                            <x-client.animal-card :animal="$animal" />
                        @endforeach
                    </div>
                @endif

                @if($closeAnimals->isNotEmpty())
                    <h3 class="subtitle text-center mt-6">{{ __('client.animals.search.close_matches_title') }}</h3>
                    <p class="labor-text text-center">{{ __('client.animals.search.close_matches_hint') }}</p>
                    <div class="grid gap-6 w-full max-w-[120rem] sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach($closeAnimals as $animal)
                            <x-client.animal-card :animal="$animal" />
                        @endforeach
                    </div>
                @endif
            @endif
        </section>
    </main>
</x-layout.app>
