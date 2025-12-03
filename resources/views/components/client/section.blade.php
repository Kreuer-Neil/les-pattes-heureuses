@props([
    'align' => 'right',
    'class',
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
        default => 'lg:-order-1',
    };
@endphp

<article class="client-bgimg general-section lg:flex-row lg:gap-3 min-h-[32rem] xl:min-h-[42rem] 2xl:min-h-[50rem]">
        <div class="flex flex-col gap-6 items-center">
            <h2 class="title text-center flex flex-col gap-2 items-center">{{ $content['title'] }}
                @if(isset($content['subtitle']))
                    <span class="subtitle text-center">{{ $content['subtitle'] }}</span>
                @endif
            </h2>
            <p class="labor-text">
                {{ $content['text'] }}
            </p>
            @if(isset($button))
                <a href="{{ $button['url'] }}" class="custom-btn">{{ __('client.homepage.buttons.' . $button['text']) }}</a>
            @endif
        </div>
        <img src="{{ $img['url'] }}" alt="{{ __('client.img.' . $img['alt']) }}"
             class="rounded-lg mx-3 max-w-3xl w-full lg:w-3/5 aspect-[570/290] {{ $alignmentClasses }}">
</article>
