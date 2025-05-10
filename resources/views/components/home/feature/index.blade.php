@props(['title', 'description', 'icon'])
<flux:card class="space-y-4 p-4 sm:p-6">
    <div class="flex items-center gap-4">
        <div class="flex size-10 xs:size-12 items-center justify-center rounded-lg bg-indigo-100 dark:bg-indigo-500/30">
            <flux:icon :$icon class="size-6 xs:size-8 text-indigo-600 dark:text-indigo-300" />
        </div>
        <h3 class="text-base xs:text-xl font-semibold xs:font-medium text-zinc-900 dark:text-white">
            {{ $title }}
        </h3>
    </div>
    <flux:text>
        <p class="leading-relaxed">
            {{ $description }}
        </p>
    </flux:text>
</flux:card>
