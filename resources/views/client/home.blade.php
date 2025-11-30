<x-layout.app>
    <x-client.navbar />
    <main>
        <h1 class="sr-only">{{ config('app.name', 'Les pattes heureuses') . ' - ' . __('client.nav.home') }}</h1>

        <x-client.section align="right"
                          :content="__('client.homepage.article1')"
                          :img="[
                            'url' => '/',
                            'alt' => 'cute_dog',
                          ]"
                          :button="[
                            'text' => 'Test',
                            'href' => '/'
                          ]"
        >
        </x-client.section>
    </main>
</x-layout.app>
