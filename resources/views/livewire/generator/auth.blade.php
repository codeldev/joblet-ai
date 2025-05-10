<flux:modal
    name="auth-required"
    class="min-w-[22rem]">
    <div class="space-y-6">
        <div>
            <flux:heading size="lg">
                {{ trans('auth.generation.modal.title') }}
            </flux:heading>
            <flux:text class="mt-2">
                <p>{{ trans('auth.generation.modal.description') }}</p>
            </flux:text>
        </div>
        <flux:button
            variant="primary"
            icon="user-round"
            :href="route('auth')"
            class="h-11 w-full"
        >
            {{ trans('auth.generation.modal.button') }}
        </flux:button>
    </div>
</flux:modal>
