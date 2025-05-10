<div>
    <flux:card class="space-y-4 sm:space-y-6 p-5 sm:p-6">
        <div>
            <flux:heading size="lg">
                {{ trans('account.delete.title') }}
            </flux:heading>
            <flux:subheading class="!leading-relaxed">
                {{ trans('account.delete.description') }}
            </flux:subheading>
        </div>
        <flux:modal.trigger name="confirm-deletion">
            <flux:button
                variant="danger"
                icon="user-round-x"
                class="h-11 w-full sm:w-auto"
            >
                {{ trans('account.delete.button') }}
            </flux:button>
        </flux:modal.trigger>
    </flux:card>
    <flux:modal
        name="confirm-deletion"
        class="max-w-md"
        wire:close="cancel"
        wire:cancel="cancel"
    >
        <form
            wire:submit="submit"
            class="space-y-6"
        >
            <flux:heading size="lg" class="flex items-center gap-2">
                <flux:icon.user-round-x class="size-5" />
                <span>{{ trans('account.delete.confirm.title') }}</span>
            </flux:heading>
            <flux:subheading>
                {{ trans('account.delete.confirm.description') }}
            </flux:subheading>

            <flux:input
                wire:model="form.password"
                :label="trans('account.delete.confirm.password')"
                :description="trans('account.delete.confirm.password.description')"
                type="password"
                icon="lock-keyhole"
                viewable
            />
            <div class="grid grid-cols-2 gap-4">
                <flux:modal.close>
                    <flux:button variant="filled" icon="x-mark" class="w-full">
                        {{ trans('account.delete.confirm.cancel') }}
                    </flux:button>
                </flux:modal.close>
                <flux:button
                    variant="danger"
                    type="submit"
                    class="w-full"
                    icon="user-round-x"
                >
                    {{ trans('account.delete.confirm.submit') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
