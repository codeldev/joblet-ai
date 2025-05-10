@props(['generation'])
<flux:card
    class="relative p-4 md:p-5 lg:p-6"
    wire:key="{{ $generation->id }}"
>
    <div class="space-y-2">
        <div>
            <div class="flex items-center justify-between gap-4 mb-1.5">
                <flux:heading class="!mb-0 !text-sm sm:!text-base">
                    {{ $generation->job_title }}
                </flux:heading>
                <x-generated.menu :$generation />
            </div>
            <flux:subheading class="!m-0">
                {{ trans('letter.dashboard.company.date', [
                    'company' => $generation->company ?? trans('misc.not.set') ,
                    'date'    => $generation->created_at->format('d/m/Y'),
                ]) }}
            </flux:subheading>
        </div>
        <flux:text>
            <p>
                {{ trans('letter.dashboard.manager.name', [
                    'manager' => $generation->manager ?? trans('misc.not.set')
                ]) }}
            </p>
        </flux:text>
    </div>
</flux:card>
