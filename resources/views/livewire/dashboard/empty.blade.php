<flux:card class="p-4 xs:p-5 lg:p-6 space-y-4 lg:space-y-6">
    <div>
        <flux:heading size="lg">
            {{ trans('dashboard.no.letters.title') }}
        </flux:heading>
        <flux:subheading>
            {{ trans('dashboard.no.letters.description') }}
        </flux:subheading>
    </div>
    <flux:button
        variant="primary"
        icon="file-text"
        class="max-xs:w-full"
        :href="route('generator')"
        wire:navigate
    >
        {{ trans('dashboard.no.letters.button') }}
    </flux:button>
</flux:card>
