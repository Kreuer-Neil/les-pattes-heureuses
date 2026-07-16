@props(['animal'])

<a href="{{ route('client.animal.show', $animal) }}" class="animal-card">
    <div class="relative">
        <img src="{{ $animal->image ? asset('storage/' . $animal->image) : asset('images/cat_dog.jpg') }}"
             alt="{{ $animal->name }}" class="animal-card-img">
        @if($animal->status)
            <span class="status-tag">{{ __('client.animal.statuses.' . $animal->status->name) }}</span>
        @endif
    </div>
    <div class="flex flex-col gap-1 p-4">
        <h3 class="subtitle">{{ $animal->name }}</h3>
        <p class="text-lg">{{ $animal->breed?->label ?? __('client.species.' . $animal->specie?->name) }}</p>
    </div>
</a>