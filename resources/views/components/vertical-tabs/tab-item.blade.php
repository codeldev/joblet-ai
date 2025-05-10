@props(['label', 'icon', 'tab'])
<flux:navlist.item
    :$icon
    x-on:click="$wire.tab = '{{ $tab }}'"
    class="!h-11 !gap-1.5 border"
    x-bind:class="$wire.tab === '{{ $tab }}'
        ? '!bg-transparent hover:!bg-transparent dark:!bg-transparent hover:dark:!bg-transparent border-indigo-600/80 dark:border-indigo-300/80 !text-indigo-600 hover:!text-indigo-600 dark:!text-indigo-300 hover:dark:!text-indigo-300'
        : 'bg-zinc-50 hover:!bg-white hover:shadow-md !text-zinc-600 dark:!bg-zinc-800 hover:dark:!bg-zinc-900 dark:!text-neutral-300/80 hover:dark:shadow-none border-transparent'
    "
>
    {{ $label }}
</flux:navlist.item>
