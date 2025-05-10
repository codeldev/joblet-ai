<flux:modal
    name="letter-settings"
    variant="flyout"
    position="left"
    class="max-w-sm sm:max-w-md md:max-w-lg !p-6 lg:!p-8 shadow-lg"
>
    <div class="space-y-4 small-description">
        <div>
            <flux:heading
                size="xl"
                class="flex items-center gap-2"
            >
                <flux:icon.adjustments-horizontal class="size-8" />
                {{ trans('generator.letter.settings.title') }}
            </flux:heading>
            <flux:subheading>
                {{ trans('generator.letter.settings.description') }}
            </flux:subheading>
        </div>

        <flux:separator variant="subtle" />

        <div class="space-y-5">
            <flux:radio.group
                wire:model.integer="form.option_creativity"
                :label="trans('generator.letter.creativity.title')"
                :description="trans('generator.letter.creativity.description')"
                variant="cards"
                class="flex-col md:flex-row"
            >
                @foreach($this->creativityOptions as $creativityId => $creativityDetails)
                    <flux:radio
                        class="generator"
                        :value="$creativityId"
                        :label="$creativityDetails['label']"
                    />
                @endforeach
            </flux:radio.group>
            <flux:badge
                color="purple"
                class="w-full !font-normal !p-2.5 justify-center !whitespace-normal"
            >
                @foreach($this->creativityOptions as $creativityId => $creativityDetails)
                    <div wire:show="form.option_creativity == {{ $creativityId }}">
                        {{ $creativityDetails['description'] }}
                    </div>
                @endforeach
            </flux:badge>
        </div>

        <flux:separator variant="subtle" />

        <div class="space-y-5">
            <flux:radio.group
                wire:model.integer="form.option_tone"
                :label="trans('generator.letter.tone.title')"
                :description="trans('generator.letter.tone.description')"
                variant="cards"
                class="flex-col md:flex-row"
            >
                @foreach($this->toneOptions as $toneId => $toneDetails)
                    <flux:radio
                        class="generator"
                        :value="$toneId"
                        :label="$toneDetails['label']"
                    />
                @endforeach
            </flux:radio.group>
            <flux:badge
                color="purple"
                class="w-full !font-normal !p-2.5 justify-center !whitespace-normal"
            >
                @foreach($this->toneOptions as $toneId => $toneDetails)
                    <div wire:show="form.option_tone == {{ $toneId }}">
                        {{ $toneDetails['description'] }}
                    </div>
                @endforeach
            </flux:badge>
        </div>

        <flux:separator variant="subtle" />

        <div class="space-y-5">
            <flux:radio.group
                wire:model.integer="form.option_length"
                :label="trans('generator.letter.length.title')"
                :description="trans('generator.letter.length.description')"
                variant="cards"
                class="flex-col md:flex-row"
            >
                @foreach($this->lengthOptions as $lengthId => $lengthDetails)
                    <flux:radio
                        class="generator"
                        :value="$lengthId"
                        :label="$lengthDetails['label']"
                    />
                @endforeach
            </flux:radio.group>
            <flux:badge
                color="purple"
                class="w-full !font-normal !p-2.5 justify-center !whitespace-normal"
            >
                @foreach($this->lengthOptions as $lengthId => $lengthDetails)
                    <div wire:show="form.option_length == {{ $lengthId }}">
                        {{ $lengthDetails['description'] }}
                    </div>
                @endforeach
            </flux:badge>
        </div>
    </div>
</flux:modal>
