@props(['label', 'icon', 'tab'])
<flux:navlist.item
    :$icon
    x-on:click="$wire.tab = '{{ $tab }}'"
    class="!h-11 !gap-1.5"
    x-bind:class="$wire.tab === '{{ $tab }}'
        ? '!bg-accent hover:!bg-accent dark:!bg-accent hover:dark:!bg-accent !text-white hover:!text-white'
        : 'bg-zinc-50 hover:!bg-white hover:shadow-md !text-zinc-600 dark:!bg-zinc-800 hover:dark:!bg-zinc-900 dark:!text-neutral-300/80 hover:dark:shadow-none'
    "
>
    {{ $label }}
</flux:navlist.item>
