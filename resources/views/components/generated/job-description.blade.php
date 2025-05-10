@props(['generated'])
<x-generated.panel>
    <flux:text>
        {!! cleanNl2br($generated->job_description) !!}
    </flux:text>
</x-generated.panel>
