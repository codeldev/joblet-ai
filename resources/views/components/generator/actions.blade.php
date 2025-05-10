@props(['creditsRequired'])
<div class="w-full flex flex-col-reverse sm:flex-row items-center justify-between gap-4">
    <flux:button
        variant="outline"
        class="h-11 max-sm:w-full"
        icon="pen-off"
        wire:click="clearForm"
    >
        {{ trans('generator.clear.form') }}
    </flux:button>
    @if($creditsRequired)
        <x-generator.submit-credits />
    @else
        <x-generator.submit-default />
    @endif
</div>
