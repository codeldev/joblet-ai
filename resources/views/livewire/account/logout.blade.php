<flux:card class="space-y-4 p-5 sm:p-6">
    <div>
        <flux:heading size="lg">
            {{ trans('account.logout.title') }}
        </flux:heading>
        <flux:subheading class="!leading-relaxed">
            {{ trans('account.logout.description') }}
        </flux:subheading>
    </div>
    <flux:button
        variant="primary"
        icon="log-out"
        class="h-11 w-full sm:w-auto"
        wire:click="submit"
    >
        {{ trans('account.logout.submit') }}
    </flux:button>
</flux:card>
