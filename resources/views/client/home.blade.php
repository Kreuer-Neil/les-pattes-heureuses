<x-layout.app>
    <x-client.navbar />
    <main class="flex flex-col items-center">
        <h1 class="sr-only">{{ config('app.name', 'Les pattes heureuses') . ' - ' . __('client.nav.home') }}</h1>

        <x-client.section align="right"
                          :content="__('client.homepage.article1')"
                          :img="[
                            'url' => asset('/images/cat_dog.jpg'),
                            'alt' => 'cat_dog',
                          ]" />
        <section class="general-section items-center bg-background-1">
            <h2 class="title">{{ __('client.stats_title') }}</h2>
            <div class="flex gap-12 items-center justify-center flex-wrap">
                @foreach($statItems as $statTitle => $statValue)
                    <x-client.stat-item :title="$statTitle" :value="$statValue" />
                @endforeach
            </div>
        </section>
        <x-client.section align="left"
                          :content="__('client.homepage.article2')"
                          :img="[
                            'url' => asset('/images/infrastructure.jpg'),
                            'alt' => 'shelter',
                          ]" />
        <x-client.section align="right"
                          :content="__('client.homepage.article3')"
                          :img="[
                            'url' => asset('/images/cat_dog.jpg'),
                            'alt' => 'cat_dog',
                          ]" />
        <x-client.section align="left"
                          :content="__('client.homepage.article4')"
                          :img="[
                            'url' => asset('/images/cat_dog.jpg'),
                            'alt' => 'cat_dog',
                          ]"
                          :button="[
                            'url' => '/',
                            'text' => 'see_animals',
                          ]" />
        <x-client.section align="right"
                          :content="__('client.homepage.article5')"
                          :img="[
                            'url' => asset('/images/cat_dog.jpg'),
                            'alt' => 'cat_dog',
                          ]"
                          :button="[
                            'url' => '/',
                            'text' => 'volunteer',
                          ]" />

        <x-client.contact-form class="bg-background-1" />
    </main>
</x-layout.app>
