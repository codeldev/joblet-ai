@props(['generation'])
<div>
    <div class="flex items-center justify-between gap-4 mb-0.5">
        <flux:heading class="!mb-0 !text-base sm:!text-lg">
            {{ $generation->job }}
        </flux:heading>
        <x-generated.menu :$generation />
    </div>
    <flux:subheading class="!m-0">
        For {{ $generation->manager }} @ {{ $generation->company }}
    </flux:subheading>
</div>
