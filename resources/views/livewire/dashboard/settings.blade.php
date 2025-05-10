<flux:modal
    name="show-settings"
    class="w-full max-w-4xl max-xs:p-3 max-md:p-4"
    wire:close="close"
    wire:cancel="close">
    @if($generated)
        <div class="space-y-6 p-2">
            <div>
                <flux:heading
                    size="xl"
                    class="flex items-center gap-2 max-md:!text-lg"
                >
                    <flux:icon.info class="max-md:size-6 size-8" />
                    {{ trans('dashboard.settings.title') }}
                </flux:heading>
                <flux:subheading>
                    {{ trans('dashboard.settings.description') }}
                </flux:subheading>
            </div>
            <flux:select wire:model.live="tab" size="sm" class="block sm:hidden h-11">
                <flux:select.option value="letter-settings">
                    {{ trans('generator.letter.settings.title') }}
                </flux:select.option>
                <flux:select.option value="letter-options">
                    {{ trans('generator.content.options.title') }}
                </flux:select.option>
                <flux:select.option value="job-description">
                    {{ trans('dashboard.generated.tab.job.description') }}
                </flux:select.option>
            </flux:select>
            <flux:tab.group class="-mt-6 sm:mt-0">
                <div class="hidden sm:block">
                    <flux:tabs wire:model="tab" variant="segmented" class="w-full h-11">
                        <flux:tab name="letter-settings" icon="adjustments-horizontal">
                            {{ trans('generator.letter.settings.title') }}
                        </flux:tab>
                        <flux:tab name="letter-options" icon="cog">
                            {{ trans('generator.content.options.title') }}
                        </flux:tab>
                        <flux:tab name="job-description" icon="info">
                            {{ trans('dashboard.generated.tab.job.description') }}
                        </flux:tab>
                    </flux:tabs>
                </div>
                <flux:tab.panel name="letter-settings" class="h-[28rem] sm:h-96">
                    <x-generated.settings :$generated />
                </flux:tab.panel>
                <flux:tab.panel name="letter-options" class="h-[28rem] sm:h-96">
                    <x-generated.options :$generated />
                </flux:tab.panel>
                <flux:tab.panel name="job-description" class="h-[28rem] sm:h-96">
                    <x-generated.job-description :$generated />
                </flux:tab.panel>
            </flux:tab.group>
        </div>
    @endif
</flux:modal>
