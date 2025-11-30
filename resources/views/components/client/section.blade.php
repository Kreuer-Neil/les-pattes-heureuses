@props([
    'align' => 'right',
//    'class' => '',
    'content' => [],
    'img' => [
        'url' => '/',
        'alt' => 'Alt text'
    ],
    'button',
])

@php
    $alignmentClasses = match ($align) {
        'left' => '',
        default => 'md:-order-1',
    };
@endphp

<article class="flex flex-col md:flex-row gap-3 items-center p-6 min-h-[32rem] client-bgimg">
    <div class="flex flex-col gap-6 items-center">
        <h2 class="title flex flex-col gap-2 items-center">{{ $content['title'] }}
        @if(isset($content['subtitle']))
            <span class="subtitle">{{ $content['subtitle'] }}</span>
        @endif
        </h2>
        <p class="labor-text">
            {{ $content['text'] }}
        </p>
        @if(isset($button))
            <a href="{{ $button['href'] }}" class="custom-btn">{{ $button['text'] }}</a>
        @endif
    </div>
    <img src="{{ $img['url'] }}" alt="{{ __('client.img.' . $img['alt']) }}"
         class="bg-gray-200 rounded mx-3 w-full md:w-[43vw] aspect-[570/290] {{ $alignmentClasses }}">
</article>
