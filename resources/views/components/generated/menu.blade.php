@props(['generation'])
<flux:dropdown
    position="bottom"
    align="end"
    offset="38"
    gap="-31"
>
    <flux:button
        icon="ellipsis-vertical"
        size="sm"
        variant="ghost"
        inset="bottom left top right"
        square
    />
    <flux:menu>
        <flux:menu.item
            icon="eye"
            wire:click="$dispatch('dashboard-view-letter', { generated: '{{ $generation->id }}'})"
        >
            {{ trans('letter.menu.view') }}
        </flux:menu.item>
        <flux:menu.separator />
        <flux:menu.item
            icon="cog"
            wire:click="$dispatch('dashboard-view-settings', { generated: '{{ $generation->id }}'})"
        >
            {{ trans('letter.menu.settings') }}
        </flux:menu.item>
        <flux:menu.separator />
        <flux:menu.item
            variant="danger"
            icon="trash"
            wire:click="$dispatch('dashboard-confirm-delete', { generated: '{{ $generation->id }}'})"
        >
            {{ trans('letter.menu.delete') }}
        </flux:menu.item>
    </flux:menu>
</flux:dropdown>
