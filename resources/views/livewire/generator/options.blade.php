<flux:card class="p-5 sm:p-6 space-y-4 scroll-mt-20" x-ref="options">
    <div>
        <flux:heading
            size="lg"
            class="flex items-center gap-2"
        >
            <flux:icon.cog class="size-5" />
            {{ trans('generator.content.options.title') }}
        </flux:heading>
        <flux:subheading>
            {{ trans('generator.content.options.description') }}
        </flux:subheading>
    </div>

    <flux:separator variant="subtle" />

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-y-3 xl:gap-y-4 gap-x-6">
        <flux:textarea
            :label="trans('generator.content.problem.title')"
            :description="trans('generator.content.problem.description')"
            wire:model="form.problem_solving_text"
            class="min-h-[90px] !resize-none"
        />

        <flux:textarea
            :label="trans('generator.content.growth.title')"
            :description="trans('generator.content.growth.description')"
            wire:model="form.growth_interest_text"
            class="min-h-[90px] !resize-none"
        />

        <flux:textarea
            :label="trans('generator.content.value.title')"
            :description="trans('generator.content.value.description')"
            wire:model="form.unique_value_text"
            class="min-h-[90px] !resize-none"
        />

        <flux:textarea
            :label="trans('generator.content.achievements.title')"
            :description="trans('generator.content.achievements.description')"
            wire:model="form.achievements_text"
            class="min-h-[90px] !resize-none"
        />

        <flux:textarea
            :label="trans('generator.content.motivation.title')"
            :description="trans('generator.content.motivation.description')"
            wire:model="form.motivation_text"
            class="min-h-[90px] !resize-none"
        />

        <flux:textarea
            :label="trans('generator.content.goals.title')"
            :description="trans('generator.content.goals.description')"
            wire:model="form.career_goals"
            class="min-h-[90px] !resize-none"
        />
    </div>

    <flux:separator  />

    <flux:textarea
        :label="trans('generator.content.other.title')"
        :description="trans('generator.content.other.description')"
        wire:model="form.other_details"
        class="min-h-[90px] !resize-none"
    />

    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 sm:gap-6">
        <flux:select
            variant="listbox"
            searchable
            indicator="checkbox"
            wire:model="form.language_variant"
            :label="trans('generator.form.language.title')"
            :description="trans('generator.form.language.description')"
            :placeholder="trans('generator.form.language.placeholder')"
        >
            @foreach($this->languages as $languageId => $variant)
                <flux:select.option :value="$languageId">
                    {{ $variant }}
                </flux:select.option>
            @endforeach
        </flux:select>
        <flux:select
            indicator="checkbox"
            variant="listbox"
            wire:model="form.date_format"
            :label="trans('generator.form.format.title')"
            :description="trans('generator.form.format.description')"
            :placeholder="trans('generator.form.format.placeholder')"
        >
            @foreach($this->dateFormats as $formatId => $format)
                <flux:select.option :value="$formatId">
                    {{ $format }}
                </flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <flux:separator />

    <flux:checkbox
        wire:model.boolean="form.include_placeholders"
        :label="trans('generator.content.option.placeholders.title')"
        :description="trans('generator.content.option.placeholders.description')"
    />
</flux:card>
