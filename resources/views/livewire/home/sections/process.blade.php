<x-home.wrapper class="bg-white dark:bg-charcoal/60 border-y border-zinc-200 dark:border-zinc-700/60 bg-texture-b-light dark:bg-texture-b-dark">
    <x-home.container class="space-y-8 md:space-y-12 lg:space-y-16">
        <x-home.header>
            <x-slot:header>
                {{ trans('home.process.title.part1') }}
                <span class="text-indigo-500 dark:text-indigo-300/80">
                    {{ trans('home.process.title.part2') }}
                </span>
            </x-slot:header>
            <x-slot:description>
                {{ trans('home.process.subtitle') }}
            </x-slot:description>
        </x-home.header>
        <div class="space-y-4 sm:space-y-8 md:space-y-12 lg:space-y-16">
            <div class="relative grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8 xl:gap-10 2xl:gap-12">
                <x-home.process
                    step="1"
                    :title="trans('home.process.list.1.title')"
                    :text="trans('home.process.list.1.text')"
                    icon="letter-text"
                />
                <x-home.process
                    step="2"
                    :title="trans('home.process.list.2.title')"
                    :text="trans('home.process.list.2.text')"
                    icon="pencil-ruler"
                />
                <x-home.process
                    class="col-span-1 md:col-span-2 lg:col-span-1"
                    step="3"
                    :title="trans('home.process.list.3.title')"
                    :text="trans('home.process.list.3.text')"
                    icon="cloud-download"
                />
            </div>

            <div class="flex flex-col sm:flex-row gap-4 sm:gap-8 justify-center">
                <flux:button
                    :href="route('auth')"
                    class="h-12"
                    variant="outline"
                    wire:navigate
                >
                    <flux:icon.user-round-plus class="size-5" />
                    <span>{{ trans('home.process.cta.button2') }}</span>
                </flux:button>
                <flux:button
                    :href="route('generator')"
                    variant="primary"
                    class="h-12"
                    wire:navigate
                >
                    <span>{{ trans('home.process.cta.button1') }}</span>
                    <flux:icon.arrow-right clas="size-5" />
                </flux:button>
            </div>
        </div>
    </x-home.container>
</x-home.wrapper>
