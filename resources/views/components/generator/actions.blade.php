@props(['creditsRequired'])
<div class="w-full flex flex-col-reverse xs:flex-row items-center justify-end gap-4">
    <flux:button
        variant="ghost"
        class="h-11 max-xs:w-full"
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
