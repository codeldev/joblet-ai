@props(['title', 'text', 'icon', 'step'])
<div {{ $attributes->merge(['class' => 'space-y-8 flex flex-col items-center text-center p-6 py-8 rounded-xl bg-white/50 dark:bg-zinc-900/30 backdrop-blur-sm border border-zinc-200/80 dark:border-zinc-700 shadow-lg']) }}
>
    <div class="relative">
        <div class="absolute -top-2 -right-2 flex h-7 w-7 items-center justify-center rounded-full bg-indigo-500 text-white text-xs font-bold shadow-md z-10 dark:bg-indigo-500">
            {{ $step }}
        </div>
        <div class="absolute -inset-1 rounded-full bg-gradient-to-r from-indigo-400 to-indigo-600 opacity-30 blur-lg dark:from-indigo-700 dark:to-indigo-500/70"></div>
        <div class="relative flex h-24 w-24 items-center justify-center rounded-full bg-zinc-100 shadow-xl dark:bg-zinc-800">
            <flux:icon
                :$icon
                class="h-12 w-12 text-indigo-500 dark:text-indigo-400/70"
            />
        </div>
    </div>
    <div class="space-y-3">
        <h3 class="text-lg xs:text-xl font-bold text-zinc-900 dark:text-white">
            {{ $title }}
        </h3>
        <flux:text>
            <p class="sm:max-w-xs mx-auto">
                {{ $text }}
            </p>
        </flux:text>
    </div>
</div>
