<form wire:submit="submit">
    <flux:card class="space-y-4 p-5 sm:p-6">
        <div class="space-y-4">
            <header class="relative space-y-5 xs:space-y-0">
                <x-generator.heading />
                <div class="xs:absolute xs:-top-1 xs:-right-1 flex items-start justify-center xs:justify-end gap-2.5">
                    <x-generator.trigger-options />
                    <x-generator.trigger-settings />
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
