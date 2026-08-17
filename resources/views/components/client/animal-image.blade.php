@props(['animal', 'sizes' => '100vw'])

@php
    $src = $animal->image
        ? asset("storage/images/animals/full/{$animal->image}.webp")
        : asset('images/cat_dog.jpg');
    $srcXs = $animal->image
        ? asset("storage/images/animals/full-xs/{$animal->image}.webp")
        : null;
@endphp

<img
    src="{{ $src }}"
    @if($srcXs)
        srcset="{{ $srcXs }} 320w, {{ $src }} 720w"
        sizes="{{ $sizes }}"
    @endif
    alt="{{ $animal->name }}"
    {{ $attributes }}
>