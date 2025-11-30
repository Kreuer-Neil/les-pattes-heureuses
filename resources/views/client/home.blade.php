<x-layout.app>
    <x-client.navbar />
    <main>
        <h1 class="sr-only">{{ config('app.name', 'Les pattes heureuses') . ' - ' . __('client.nav.home') }}</h1>

        <x-client.section align="right"
                          :content="__('client.homepage.article1')"
                          :img="[
                            'url' => '/',
                            'alt' => 'cat_dog',
                          ]"
                          {{--:button="[
                            'text' => 'Test',
                            'href' => '/'
                          ]"--}}
        />
        <section class="flex flex-col p-6 gap-6 items-center bg-background-1">
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
                            'url' => '/',
                            'alt' => 'shelter',
                          ]"
                          {{--:button="[
                            'text' => 'Test',
                            'href' => '/'
                          ]"--}}
        />
    </main>
</x-layout.app>
