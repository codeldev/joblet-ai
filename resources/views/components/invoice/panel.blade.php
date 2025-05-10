@props(['icon', 'label', 'text'])
<div class="space-y-2">
    <div class="flex items-center gap-1.5">
        <flux:icon :$icon class="size-4" />
        <h2 class="font-semibold">{{ $label }}</h2>
    </div>
    <x-invoice.badge :$text />
</div>
