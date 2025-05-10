<div class="h-full flex-1 flex items-center justify-center">
    @if($lockoutMessage)
        @include('livewire.auth.lockout')
    @else
        <div class="max-w-md mx-auto space-y-4 sm:space-y-6 lg:space-y-8">
            <div class="space-y-2">
                <flux:heading level="1" class="flex items-center justify-center gap-2">
                    <flux:icon.user-round class="size-6" />
                    {{ trans('auth.title') }}
                </flux:heading>
                <flux:text class="text-center">
                    {{ trans('auth.description') }}
                </flux:text>
            </div>
            <flux:card>
                <flux:tab.group>
                    <flux:tabs
                        wire:model.live="type"
                        variant="segmented"
                        class="max-xs:flex-col max-xs:w-full max-xs:flex max-xs:!h-20 min-xs:!h-11"
                    >
                        <flux:tab
                            name="sign-in"
                            icon="user-round-check"
                            class="max-xs:flex"
                        >
                            {{ trans('auth.sign.in') }}
                        </flux:tab>
                        <flux:tab
                            name="sign-up"
                            icon="user-round-plus"
                        >
                            {{ trans('auth.sign.up') }}
                        </flux:tab>
                    </flux:tabs>
                    <flux:tab.panel name="sign-in">
                        <livewire:auth.sign-in />
                    </flux:tab.panel>
                    <flux:tab.panel name="sign-up">
                        <livewire:auth.sign-up />
                    </flux:tab.panel>
                </flux:tab.group>
            </flux:card>
        </div>
    @endif
</div>
