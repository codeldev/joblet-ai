<div class="space-y-4 sm:space-y-6" wire:init="displayMessages">
    <div class="space-y-3">
        <flux:heading level="1" class="flex items-center justify-start gap-2">
            <flux:icon.user-round class="size-6" />
            {{ trans('account.title') }}
        </flux:heading>

        <livewire:account.order-payment />
    </div>
    <div class="block lg:hidden">
        <flux:select wire:model.live="tab">
            <flux:select.option value="profile">{{ trans('account.profile.title') }}</flux:select.option>
            <flux:select.option value="password">{{ trans('account.password.title') }}</flux:select.option>
            <flux:select.option value="orders">{{ trans('orders.title') }}</flux:select.option>
            <flux:select.option value="sessions">{{ trans('account.sessions.title') }}</flux:select.option>
            <flux:select.option value="delete">{{ trans('account.delete.title') }}</flux:select.option>
        </flux:select>
    </div>
    <x-vertical-tabs tab="profile">
        <x-vertical-tabs.tabs class="hidden lg:flex w-44 flex-none !space-y-4">
            <x-vertical-tabs.tab-item
                tab="profile"
                icon="user-round"
                :label="trans('account.profile.title')" />
            <x-vertical-tabs.tab-item
                tab="password"
                icon="lock"
                :label="trans('account.password.title')" />
            <x-vertical-tabs.tab-item
                tab="orders"
                icon="shopping-basket"
                :label="trans('orders.title')" />
            <x-vertical-tabs.tab-item
                tab="sessions"
                icon="cookie"
                :label="trans('account.sessions.title')" />
            <x-vertical-tabs.tab-item
                tab="delete"
                icon="user-round-x"
                :label="trans('account.delete.title')" />
        </x-vertical-tabs.tabs>
        <x-vertical-tabs.panels>
            <x-vertical-tabs.panel class="space-y-8" tab="profile">
                <livewire:account.profile />
                <livewire:account.logout />
            </x-vertical-tabs.panel>
            <x-vertical-tabs.panel tab="password">
                <livewire:account.password />
            </x-vertical-tabs.panel>
            <x-vertical-tabs.panel tab="orders">
                <livewire:account.orders />
            </x-vertical-tabs.panel>
            <x-vertical-tabs.panel tab="sessions">
                <livewire:account.sessions />
            </x-vertical-tabs.panel>
            <x-vertical-tabs.panel tab="delete">
                <livewire:account.delete />
            </x-vertical-tabs.panel>
        </x-vertical-tabs.panels>
    </x-vertical-tabs>
</div>
