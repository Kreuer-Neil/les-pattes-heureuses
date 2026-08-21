@php($navbarItems = [
        'home' => [
            'name'=>'client.nav.home',
            'url' => route('homepage')
        ],
        'animals' => [
            'name' => 'client.nav.animals',
            'url' => route('client.animals'),
        ],
    ])

<nav class="navbar">
    <h2 class="sr-only">{!!__('client.nav.main_nav')!!}</h2>
    <a href="{{ route('homepage') }}" class="flex min-h-16 h-full items-center justify-center px-2">
        <x-app-logo textClass="not-sm:sr-only" />
    </a>

    <ul class="flex">
        @foreach($navbarItems as $navbarItem)
            @php($isHome = $navbarItem['name'] === 'client.nav.home')
            <li class="@if($isHome) max-sm:hidden @endif">
                <a href="{{$navbarItem['url']}}"
                   class="navbar-item {{ $navbarItem['url'] == Request::url() ? 'navbar-item-active' :'' }}">
                    <span>
                        {!! ucfirst(__($navbarItem['name'])) !!}
                    </span>
                </a>
            </li>
        @endforeach
        <li>
            <button type="button" command="show-modal" commandfor="site-contact-dialog" class="navbar-item">
                <span>
                    {!! ucfirst(__('client.nav.contact_us')) !!}
                </span>
            </button>
        </li>
        <li class="relative flex h-full">
            <details class="group">
                <summary class="navbar-item list-none cursor-pointer no-underline [&::-webkit-details-marker]:hidden">
                    <span class="flex items-center gap-1 p-2 sm:px-4">
                        <span class="underline">{{ strtoupper(app()->getLocale()) }}</span>
                        <span aria-hidden="true" class="text-xs transition-transform group-open:rotate-180">▾</span>
                    </span>
                </summary>
                <ul class="absolute top-full right-0 z-10 flex min-w-full flex-col bg-sidebar shadow-lg">
                    @foreach(config('app.locales') as $locale)
                        @if($locale !== app()->getLocale())
                            <li>
                                <a href="{{ route('lang', ['lang' => $locale]) }}" class="navbar-item block text-center">
                                    <span class="inline-block p-2 px-4 border-t border-amber-800">
                                        {{ strtoupper($locale) }}
                                    </span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </details>
        </li>
    </ul>
</nav>

<x-dialogs.contact-dialog />
