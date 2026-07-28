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
            <li>
                <a href="{{$navbarItem['url']}}"
                   class="navbar-item {{ $navbarItem['url'] == Request::url() ? 'navbar-item-active' :'' }}">
                    <span class="p-2 px-4">
                        {!! ucfirst(__($navbarItem['name'])) !!}
                    </span>
                </a>
            </li>
        @endforeach
        <li>
            <button type="button" command="show-modal" commandfor="site-contact-dialog" class="navbar-item">
                <span class="p-2 px-4">
                    {!! ucfirst(__('client.nav.contact_us')) !!}
                </span>
            </button>
        </li>
    </ul>
</nav>

<x-dialogs.contact-dialog />
