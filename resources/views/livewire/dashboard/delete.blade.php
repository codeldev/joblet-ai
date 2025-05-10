<flux:modal
    name="confirm-deletable"
    class="min-w-[22rem]"
    wire:close="cancel"
    wire:cancel="cancel">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">
                {{ trans('letter.delete.modal.title') }}
            </flux:heading>
            <flux:text class="mt-2">
                <p>{{ trans('letter.delete.modal.description') }}</p>
            </flux:text>
        </div>
        <div class="flex gap-2">
            <flux:spacer />
            <flux:modal.close>
                <flux:button variant="ghost">
                    {{ trans('misc.word.cancel.close') }}
                </flux:button>
            </flux:modal.close>
            <flux:button variant="danger" wire:click="delete">
                {{ trans('letter.delete.modal.submit') }}
            </flux:button>
        </div>
    </div>
</flux:modal>
