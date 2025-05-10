@props(['title', 'target'])
<flux:card class="p-4 mt-5" x-show="{{ $target }}" x-cloak>
    <div class="flex items-center justify-between gap-2">
        <flux:heading level="4">{{ $title }}</flux:heading>
        <flux:button
            size="sm"
            variant="ghost"
            inset="top left bottom right"
            x-on:click="closeAll"
            square
        >
            <flux:icon.x-mark class="size-4" />
        </flux:button>
    </div>
    <flux:text class="pt-4">
        <ul class="list-disc space-y-1 pl-3 xs:pl-5">
            {{ $slot }}
        </ul>
    </flux:text>
</flux:card>
