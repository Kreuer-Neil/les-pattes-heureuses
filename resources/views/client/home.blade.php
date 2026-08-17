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
                            'url' => route('client.animals'),
                            'text' => 'see_animals',
                          ]" />
        <x-client.section align="right"
                          :content="__('client.homepage.article5')"
                          :img="[
                            'url' => asset('/images/cat_dog.jpg'),
                            'alt' => 'cat_dog',
                          ]"
                          :button="[
                            'dialog' => 'volunteer-dialog',
                            'text' => 'volunteer',
                          ]" />

        <section class="general-section items-center bg-background-1" id="contact">
            <h2 class="title text-center">{{ __('client.contact.title') }}</h2>
            <p class="labor-text text-center">{{ __('client.contact.text') }}</p>
            <button type="button" command="show-modal" commandfor="site-contact-dialog" class="custom-btn w-fit">
                {{ __('client.contact.cta') }}
            </button>
        </section>
    </main>

    <x-dialogs.volunteer-dialog />
</x-layout.app>
