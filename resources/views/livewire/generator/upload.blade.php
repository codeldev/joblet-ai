<flux:field class="space-y-4">
    <div class="space-y-1.5">
        <flux:label>
            {{ trans('generator.form.resume.title') }}
        </flux:label>
        <flux:description>
            @if($fileName)
                {!! trans('generator.form.resume.stored', [
                    'filename' => '<span class="text-theme-700 dark:text-theme-400/80">' . $fileName . '</span>'
                ]) !!}
            @else
                {{ trans('generator.form.resume.description') }}
            @endif
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
        <flux:input
            type="file"
            wire:model.live="file"
            accept=".pdf" />
        <flux:error name="file" />
    </div>
</flux:field>
