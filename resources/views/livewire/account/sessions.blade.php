<div>
    <flux:card class="p-5 sm:p-6">
        <div class="space-y-4">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <flux:heading size="lg">
                        {{ trans('account.sessions.title') }}
                    </flux:heading>
                    <flux:subheading class="!leading-relaxed">
                        {{ trans('account.sessions.description') }}
                    </flux:subheading>
                </div>
                <flux:modal.trigger name="clear-sessions" class="hidden lg:flex">
                    <flux:button
                        variant="primary"
                        icon="cookie"
                    >
                        {{ trans('account.sessions.button') }}
                    </flux:button>
                </flux:modal.trigger>
            </div>
            <flux:card class="py-3">
                <flux:table>
                    <flux:table.columns>
                        <flux:table.column>
                            {{ trans('account.sessions.os') }}
                        </flux:table.column>
                        <flux:table.column>
                            {{ trans('account.sessions.device') }}
                        </flux:table.column>
                        <flux:table.column>
                            {{ trans('account.sessions.browser') }}
                        </flux:table.column>
                        <flux:table.column>
                            {{ trans('account.sessions.ip') }}
                        </flux:table.column>
                        <flux:table.column>
                            {{ trans('account.sessions.active') }}
                        </flux:table.column>
                        <flux:table.column class="w-16">
                            {{ trans('account.sessions.state') }}
                        </flux:table.column>
                    </flux:table.columns>
                    <flux:table.rows>
                        @foreach($this->sessions as $index => $session)
                            <flux:table.row wire:key="session-{{ $index }}">
                                <flux:table.cell>
                                    {{ $session->device['platform'] }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $session->deviceType }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $session->device['browser'] }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:link variant="subtle" :href="trans('account.sessions.lookup', [
                                'ip' => $session->ip_address
                            ])" external>
                                        {{ $session->ip_address }}
                                    </flux:link>
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $session->last_active }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    <flux:badge
                                        icon="{{ $session->is_current_device ? 'check' : 'lightbulb' }}"
                                        color="{{ $session->is_current_device ? 'pink' : 'sky' }}"
                                        size="sm"
                                        inset="top bottom"
                                        class="w-full h-7 justify-center !text-[0.8rem]"
                                    >
                                        {{ trans($session->is_current_device
                                            ? 'account.sessions.state.current'
                                            : 'account.sessions.state.other'
                                        ) }}
                                    </flux:badge>
                                </flux:table.cell>
                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </flux:card>
        </div>
        <div class="block mt-5 lg:hidden">
            <flux:modal.trigger name="clear-sessions">
                <flux:button
                    variant="primary"
                    icon="cookie"
                    class="w-full sm:w-auto"
                >
                    {{ trans('account.sessions.button') }}
                </flux:button>
            </flux:modal.trigger>
        </div>
    </flux:card>
    <flux:modal
        name="clear-sessions"
        class="max-w-md"
        wire:close="cancel"
        wire:cancel="cancel"
    >
        <form
            wire:submit="submit"
            class="space-y-6"
        >
            <flux:heading size="lg" class="flex items-center gap-2">
                <flux:icon.cookie class="size-5" />
                <span>{{ trans('account.sessions.modal.title') }}</span>
            </flux:heading>
            <flux:subheading>
                {{ trans('account.sessions.modal.description') }}
            </flux:subheading>

            <div class="-mt-2">
                <flux:input
                    wire:model="form.password"
                    :label="trans('account.sessions.modal.password')"
                    type="password"
                    icon="lock-keyhole"
                    viewable
                />
            </div>
            <div class="grid grid-cols-2 gap-4">
                <flux:modal.close>
                    <flux:button
                        variant="filled"
                        icon="x-mark"
                        class="w-full"
                    >
                        {{ trans('misc.word.cancel.close') }}
                    </flux:button>
                </flux:modal.close>
                <flux:button
                    variant="primary"
                    type="submit"
                    icon="shield-check"
                    class="w-full"
                >
                    {{ trans('account.sessions.modal.submit') }}
                </flux:button>
            </div>
        </form>
    </flux:modal>
</div>
