@props([
    'align' => 'right',
    'class',
    'content' => [],
    'img',
    'button',
])

@php
    $alignmentClasses = match ($align) {
        'left' => '',
        default => 'lg:-order-1',
    };
@endphp

<article class="bg-decoration general-section lg:flex-row lg:gap-3 min-h-[32rem] xl:min-h-[42rem] 2xl:min-h-[50rem]">
        <div class="flex flex-col gap-6 items-center">
            <h2 class="title text-center flex flex-col gap-2 items-center">{!! $content['title'] !!}
                @if(isset($content['subtitle']))
                    <span class="subtitle text-center">{!! $content['subtitle'] !!}</span>
                @endif
            </h2>
            <p class="labor-text">
                {!! $content['text'] !!}
            </p>
            @if(isset($button))
                @if(isset($button['dialog']))
                    <button type="button" command="show-modal" commandfor="{{ $button['dialog'] }}" class="custom-btn">
                        {{ __('client.homepage.buttons.' . $button['text']) }}
                    </button>
                @else
                    <a href="{{ $button['url'] }}" class="custom-btn">{{ __('client.homepage.buttons.' . $button['text']) }}</a>
                @endif
            @endif
        </div>
        <img src="{{ asset("images/frontpage/$img.jpg") }}"
             srcset="{{ asset("images/frontpage-sm/$img.jpg") }} 354w,
                     {{ asset("images/frontpage/$img.jpg") }} 768w,
                     {{ asset("images/frontpage-x2/$img.jpg") }} 1536w"
             sizes="(min-width: 1024px) min(60vw, 48rem), min(100vw, 48rem)"
             alt="{{ __('client.img.' . $img) }}"
             class="rounded-lg mx-3 max-w-3xl w-full lg:w-3/5 aspect-[570/290] {{ $alignmentClasses }} object-cover">
</article>
