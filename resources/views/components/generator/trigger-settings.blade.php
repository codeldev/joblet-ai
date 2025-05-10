<flux:modal.trigger name="letter-settings">
    <flux:tooltip :content="trans('generator.letter.settings.title')">
        <flux:button
            type="button"
            variant="outline"
            square
            size="sm"
            inset="top bottom"
            wire:loading.attr="disabled"
        >
            <flux:icon.adjustments-horizontal class="size-5" />
        </flux:button>
    </flux:tooltip>
</flux:modal.trigger>
