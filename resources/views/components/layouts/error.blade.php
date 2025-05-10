@props(['title', 'description', 'code'])
<x-layouts.base :$title>
    <div class="flex items-center justify-center min-h-screen px-5">
        <div class="w-full max-w-lg flex flex-col justify-center items-center gap-6 sm:gap-8">
            <a href="{{ route('home') }}">
                <x-brand size="lg" />
            </a>
            <div class="text-center rounded-xl p-6 bg-white dark:bg-black/15">
                <flux:heading level="1" class="!text-base xs:!text-lg text-indigo-600 dark:text-indigo-500 !font-medium">
                    {{ $title }}
                </flux:heading>
                <flux:subheading clas="leading-relaxed text-center">
                    {{ $description }}
                </flux:subheading>
            </div>
        </div>

    </div>
</x-layouts.base>
