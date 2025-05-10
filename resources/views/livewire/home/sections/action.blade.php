<x-home.wrapper>
    <x-home.container class="space-y-8 md:space-y-12 lg:space-y-16">
        <x-home.header>
            <x-slot:header>
                <div class="flex flex-col text-3xl sm:text-4xl md:text-5xl lg:text-6xl mx-auto font-extrabold gap-1.5">
                    <span>{{ trans('home.action.title.part1') }}</span>
                    <span>
                        <span class="text-indigo-500 dark:text-indigo-300/80">{{ trans('home.action.title.part2') }}</span>
                        {{ trans('home.action.title.part3') }}
                    </span>
                </div>
            </x-slot:header>
            <x-slot:description>
                {{ trans('home.action.subtitle') }}
            </x-slot:description>
        </x-home.header>
        <div class="px-4 sm:px-0 grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16">
            <x-home.preview
                :letter="$letters[1]"
                preview-id="action-preview-1"
                x-typewriter="[
                {{ json_encode($letters[1]['lines'], JSON_THROW_ON_ERROR) }},
                'action-preview-1', {
                    completionMessage: '{{ trans('home.preview.example.letter.continues') }}'
                }]"
            />
            <x-home.preview
                :letter="$letters[2]"
                preview-id="action-preview-2"
                x-typewriter="[
                {{ json_encode($letters[2]['lines'], JSON_THROW_ON_ERROR) }},
                'action-preview-2', {
                    startDelay: 1500,
                    completionMessage: '{{ trans('home.preview.example.letter.continues') }}'
                }]"
            />
        </div>
        <div class="flex flex-col xs:flex-row gap-4 sm:gap-8 justify-center">
            <flux:button
                :href="route('auth')"
                class="h-12 w-full xs:w-auto"
                variant="outline"
                wire:navigate
            >
                <flux:icon.user-round-plus class="size-5" />
                <span>{{ trans('home.action.button1') }}</span>
            </flux:button>
            <flux:button
                :href="route('generator')"
                variant="primary"
                class="h-12 w-full xs:w-auto"
                wire:navigate
            >
                <span>{{ trans('home.action.button2') }}</span>
                <flux:icon.arrow-right clas="size-5" />
            </flux:button>
        </div>
    </x-home.container>
</x-home.wrapper>
