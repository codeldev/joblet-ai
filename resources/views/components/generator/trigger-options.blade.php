<flux:modal.trigger name="letter-options">
    <flux:tooltip :content="trans('generator.content.options.title')">
        <flux:button
            type="button"
            variant="outline"
            size="sm"
            inset="top bottom"
            square
            wire:loading.attr="disabled"
        >
            <flux:icon.cog class="size-6" />
        </flux:button>
    </flux:tooltip>
</flux:modal.trigger>
