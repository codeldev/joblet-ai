<flux:field class="space-y-4">
    <div class="space-y-1.5">
        <flux:label>
            {{ trans('generator.form.resume.title') }}
        </flux:label>
        <flux:description>
            {{ $fileName
                ? trans('generator.form.resume.stored')
                : trans('generator.form.resume.description')
            }}
        </flux:description>
    </div>
    <div
        wire:show="processing"
        wire:cloak
    >
        <flux:badge color="emerald" icon="loading" class="h-10 w-full gap-2">
            {{ trans('generator.form.resume.upload.progress') }}
        </flux:badge>
    </div>
    <div
        wire:key="{{ $uploadKey }}"
        wire:show="!processing"
        wire:cloak
    >
        <div class="flex flex-col-reverse md:flex-row items-center gap-4">
            <div class="grow w-full md:w-auto">
                <flux:input
                    type="file"
                    wire:model.live="file"
                    accept=".pdf"
                />
            </div>
            @if($fileName)
                <div class="grow w-full">
                    <flux:badge color="emerald" class="w-full h-10 pl-3 line-clamp-1 !text-sm" icon="paperclip">
                        <div class="truncate">{{ $fileName }}</div>
                    </flux:badge>
                </div>
            @endif
        </div>
        <flux:error name="file" />
    </div>
</flux:field>
