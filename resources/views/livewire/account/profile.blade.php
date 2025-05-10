<flux:card class="space-y-4 p-5 sm:p-6">
    <div>
        <flux:heading size="lg">
            {{ trans('account.profile.title') }}
        </flux:heading>
        <flux:subheading class="!leading-relaxed">
            {{ trans('account.profile.description') }}
        </flux:subheading>
    </div>

    <form wire:submit="submit" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
            <flux:input
                type="text"
                wire:model="form.name"
                :label="trans('account.profile.name')"
            />
            <flux:input
                type="email"
                wire:model="form.email"
                :label="trans('account.profile.email')"
            />
        </div>
        <flux:button
            variant="primary"
            type="submit"
            class="h-11 w-full sm:w-auto"
            icon="save"
        >
            {{ trans('account.profile.submit') }}
        </flux:button>
    </form>
</flux:card>
