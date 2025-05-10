<flux:card class="space-y-4 p-5 sm:p-6">
    <div>
        <flux:heading size="lg">
            {{ trans('account.password.title') }}
        </flux:heading>
        <flux:subheading class="!leading-relaxed">
            {{ trans('account.password.description') }}
        </flux:subheading>
    </div>
    <form wire:submit="submit" class="space-y-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
            <flux:input
                wire:model="form.password"
                :label="trans('account.password.form.new')"
                type="password"
                icon="lock-keyhole"
                viewable
            />
            <flux:input
                wire:model="form.confirmed"
                :label="trans('account.password.form.confirm')"
                type="password"
                icon="lock"
                viewable
            />
        </div>
        <flux:button
            variant="primary"
            icon="refresh-cw"
            type="submit"
            class="h-11 w-full sm:w-auto"
        >
            {{ trans('account.password.form.submit') }}
        </flux:button>
    </form>
</flux:card>
