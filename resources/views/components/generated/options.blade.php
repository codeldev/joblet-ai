@props(['generation'])
<x-generated.toggle-container
    :title="trans('dashboard.generated.options.title')"
    target="showOptions"
>
    <li>
        {{ $generation->leaving_reason
            ? trans('dashboard.generated.options.reason.set')
            : trans('dashboard.generated.options.reason.unset') }}
    </li>
    <li>
        {{ $generation->transition_assistance
            ? trans('dashboard.generated.options.assistance.set')
            : trans('dashboard.generated.options.assistance.unset') }}
    </li>
    <li>
        {{ $generation->express_gratitude
            ? trans('dashboard.generated.options.gratitude.set')
            : trans('dashboard.generated.options.gratitude.unset') }}
    </li>
    <li>
        {{ $generation->positive_experience
            ? trans('dashboard.generated.options.experience.set')
            : trans('dashboard.generated.options.experience.unset') }}
    </li>
    <li>
        {{ $generation->include_placeholders
            ? trans('dashboard.generated.options.placeholders.set')
            : trans('dashboard.generated.options.placeholders.unset') }}
    </li>
</x-generated.toggle-container>
