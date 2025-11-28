@php($navbarItems = [
        'home' => [
            'name'=>'client.nav.home',
            'url' => route('homepage')
        ],
        'animals' => [
            'name' => 'client.nav.animals',
            'url' => route('homepage') /*route('client.animals')*/,
        ],
        '' => [
            'name' => 'client.nav.contact_us',
            'url' => route('homepage', ['volounteering' => true]) . '#contact'
        ]
    ])

<nav class="navbar">
    <h2 class="sr-only">{{__('client.nav.main-nav')}}</h2>
    <a href="{{ route('homepage') }}" class="flex min-h-16 h-full items-center justify-center px-2">
            <x-app-logo  />

    </a>

    <ul class="flex">
        @foreach($navbarItems as $navbarItem)
            <li>
                <a href="{{$navbarItem['url']}}"
                   class="navbar-item z-10 {{ $navbarItem['url'] == Request::url() ? 'navbar-item-active' :'' }}">
                    <span class="p-2 px-4">
                        {{ ucfirst(__($navbarItem['name'])) }}
                    </span>
                </a>
            </li>
        @endforeach
    </ul>
</nav>
