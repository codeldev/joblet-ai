<form wire:submit="submit">
    <flux:card class="space-y-4 p-5 sm:p-6">
        <div class="space-y-6 md:space-y-4">
            <header class="relative space-y-5">
                <x-generator.heading />
                <div class="md:absolute md:-top-0 md:-right-0 flex items-start justify-center md:justify-end gap-4">
                    <flux:button
                        type="button"
                        variant="outline"
                        inset="top bottom"
                        size="sm"
                        class="h-9"
                        x-on:click="$refs.options.scrollIntoView()"
                    >
                        <flux:icon.cog class="size-4" />
                        {{ trans('generator.content.options.title') }}
                    </flux:button>
                    <flux:button
                        type="button"
                        variant="outline"
                        inset="top bottom"
                        size="sm"
                        class="h-9"
                        x-on:click="$refs.settings.scrollIntoView()"
                    >
                        <flux:icon.adjustments-horizontal class="size-4" />
                        {{ trans('generator.letter.settings.title') }}
                    </flux:button>
                </div>
            </header>
            <flux:separator />
        </div>
        <div class="space-y-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-y-3 gap-x-6">
                <div class="space-y-4">
                    <flux:input
                        type="text"
                        wire:model="form.name"
                        :label="trans('generator.form.name.title')"
                        :description="trans('generator.form.name.description')"
                    />
                    <flux:input
                        type="text"
                        wire:model="form.company"
                        :label="trans('generator.form.company.title')"
                        :description="trans('generator.form.company.description')"
                    />
                    <flux:input
                        type="text"
                        wire:model="form.manager"
                        :label="trans('generator.form.manager.title')"
                        :description="trans('generator.form.manager.description')"
                    />
                    <flux:input
                        type="text"
                        wire:model="form.job_title"
                        :label="trans('generator.form.job.title')"
                        :description="trans('generator.form.job.description')"
                    />
                </div>
                <div class="space-y-4">
                    <flux:textarea
                        wire:model="form.job_description"
                        :label="trans('generator.form.job.details.title')"
                        :description="trans('generator.form.job.details.description')"
                        class="min-h-[125px] lg:min-h-[270px]"
                    />
                    <livewire:generator.upload />
                </div>
            </div>
            <flux:separator />
            <x-generator.actions
                :credits-required="$this->creditsRequired"
            />
        </div>
    </flux:card>
</form>
