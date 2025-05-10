<x-home.wrapper>
    <x-home.container>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 xs:gap-12 lg:gap-16">
            <div class="flex flex-col justify-center max-w-2xl mx-auto lg:mx-0 space-y-4 sm:space-y-6">
                <h1 class="font-bold tracking-tight leading-tight text-zinc-900 dark:text-white text-4xl sm:text-5xl text-center lg:text-left">
                    <span>{{ trans('home.hero.title.part1') }}</span>
                    <span class="text-indigo-500 dark:text-indigo-300/80">{{ trans('home.hero.title.part2') }}</span>
                    <span>{{ trans('home.hero.title.part3') }}</span>
                </h1>
                <flux:text>
                    <p class="max-w-lg sm:max-w-auto mx-auto text-sm sm:text-base leading-relaxed text-center lg:text-left">
                        {{ trans('home.hero.subtitle') }}
                    </p>
                </flux:text>
                <div class="flex flex-col xs:flex-row gap-4 lg:gap-8 justify-center lg:justify-start">
                    <flux:button
                        :href="route('generator')"
                        variant="primary"
                        class="h-12 gap-2 w-full xs:w-auto"
                        wire:navigate
                    >
                        <span>{{ trans('home.hero.ctg.button1') }}</span>
                        <flux:icon.arrow-right class="size-5"/>
                    </flux:button>
                    <flux:button
                        x-on:click.prevent="document.getElementById('benefits').scrollIntoView({behavior: 'smooth'})"
                        variant="outline"
                        class="h-12 gap-2 w-full xs:w-auto"
                    >
                        <span>{{ trans('home.hero.ctg.button2') }}</span>
                        <flux:icon.arrow-down class="size-5"/>
                    </flux:button>
                </div>
            </div>
            <x-home.preview
                :letter="$letters[0]"
                preview-id="hero-preview"
                x-typewriter="[
                {{ json_encode($letters[0]['lines'], JSON_THROW_ON_ERROR) }},
                'hero-preview', {
                    completionMessage: '{{ trans('home.preview.example.letter.continues') }}'
                }]"
            />
        </div>
    </x-home.container>
</x-home.wrapper>
