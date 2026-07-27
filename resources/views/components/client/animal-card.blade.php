@props(['animal'])

<a href="{{ route('client.animal.show', $animal) }}" class="animal-card">
    <div class="relative">
        <x-client.animal-image :animal="$animal" class="animal-card-img"
            sizes="(min-width: 1280px) 22vw, (min-width: 1024px) 30vw, (min-width: 640px) 45vw, 100vw" />
        @if($animal->status)
            <span class="status-tag">{{ __('client.animal.statuses.' . $animal->status->name) }}</span>
        @endif
    </div>
    <div class="flex flex-col gap-1 p-4">
        <h3 class="subtitle">{{ $animal->name }}</h3>
        <p class="text-lg">{{ $animal->breed?->label ?? __('client.species.' . $animal->specie?->name) }}</p>
    </div>
</a>