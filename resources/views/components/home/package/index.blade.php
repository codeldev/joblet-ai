@props(['title', 'description', 'price', 'label', 'icon', 'button', 'selected' => false])

@php
    $classes = 'p-8';
    $classes .= $selected ? ' relative !border-indigo-500 dark:!border-indigo-300/80' : '';
@endphp

<flux:card {{ $attributes->merge(['class' => $classes]) }}>
    @if($selected)
        <div class="absolute -top-5 left-0 right-0 mx-auto w-32 rounded-full py-2 px-4 text-center text-sm font-semibold text-white bg-indigo-500 dark:bg-indigo-400">
            Recommended
        </div>
    @endif
    <div class="mb-4 space-y-1">
        <h3 class="text-xl font-bold text-zinc-900 dark:text-white">{{ $title }}</h3>
        <flux:text>
            <p>{{ $description }}</p>
        </flux:text>
    </div>
    <div class="mb-6">
        <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ $price }}</p>
        <flux:text>
            <p>{{ $label }}</p>
        </flux:text>
    </div>
    <flux:text>
        <ul class="mb-8 space-y-3 text-left">
            {{ $list }}
        </ul>
    </flux:text>
    <div class="mt-auto">
        <flux:button
            href="{{ route('auth') }}"
            variant="{{ $selected ? 'primary' : 'outline' }}"
            class="w-full h-12"
            wire:navigate
        >
            <flux:icon :$icon class="size-5" />
            <span>{{ $button }}</span>
        </flux:button>
    </div>
</flux:card>
