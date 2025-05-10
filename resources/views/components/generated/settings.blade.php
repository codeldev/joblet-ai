@props(['generated'])
<x-generated.panel>
    <flux:text class="py-1.5 space-y-5">
        <ul class="list-disc space-y-1.5 pl-3 xs:pl-5">
            <li>
                {{ trans('Name: :name', [
                    'name' => $generated->name
                ]) }}
            </li>
            <li>
                {{ trans('Role: :job', [
                    'job' => $generated->job_title
                ]) }}
            </li>
            <li>
                {{ trans('Company: :company', [
                    'company' => $generated->company ?? trans('misc.not.set')
                ]) }}
            </li>
            <li>
                {{ trans('Hiring Manager: :manager', [
                    'manager' => $generated->manager ?? trans('misc.not.set')
                ]) }}
            </li>
        </ul>
        <flux:separator />

        <ul class="list-disc space-y-1.5 pl-3 xs:pl-5">
            <li>
                {{ trans('dashboard.generated.settings.language', [
                    'language' => $generated->language_variant->label()
                ]) }}
            </li>
            <li>
                {{ trans('dashboard.generated.settings.formatting', [
                    'format' => $generated->date_format->format()
                ]) }}
            </li>
            <li>
                {{ trans('dashboard.generated.settings.creativity', [
                    'creativity' => $generated->option_creativity->label()
                ]) }}. {{ $generated->option_creativity->description() }}
            </li>
            <li>
                {{ trans('dashboard.generated.settings.tone', [
                    'tone' => $generated->option_tone->label()
                ]) }}. {{ $generated->option_tone->description() }}
            </li>
            <li>
                {{ trans('dashboard.generated.settings.length', [
                    'length' => $generated->option_length->label()
                ]) }}. {{ $generated->option_length->description() }}
            </li>
        </ul>
    </flux:text>
</x-generated.panel>
