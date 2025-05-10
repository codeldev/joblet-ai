@props(['generated'])
<x-generated.panel>
    <flux:text class="py-1.5">
        <ul class="list-disc space-y-4 pl-3 xs:pl-5">
            <li>
                {{ $generated->include_placeholders
                    ? trans('dashboard.generated.options.placeholders.set')
                    : trans('dashboard.generated.options.placeholders.unset') }}
            </li>
            @if(notEmpty($generated->problem_solving_text))
                <li class="space-y-2">
                    <flux:heading level="3">{{ trans('generator.content.problem.title') }}</flux:heading>
                    <flux:text class="leading-relaxed">{!! cleanNl2br($generated->problem_solving_text) !!}</flux:text>
                </li>
            @endif
            @if(notEmpty($generated->growth_interest_text))
                <li class="space-y-2">
                    <flux:heading level="3">{{ trans('generator.content.growth.title') }}</flux:heading>
                    <flux:text class="leading-relaxed">{!! cleanNl2br($generated->growth_interest_text) !!}</flux:text>
                </li>
            @endif
            @if(notEmpty($generated->unique_value_text))
                <li class="space-y-2">
                    <flux:heading level="3">{{ trans('generator.content.value.title') }}</flux:heading>
                    <flux:text class="leading-relaxed">{!! cleanNl2br($generated->unique_value_text) !!}</flux:text>
                </li>
            @endif
            @if(notEmpty($generated->achievements_text))
                <li class="space-y-2">
                    <flux:heading level="3">{{ trans('generator.content.achievements.title') }}</flux:heading>
                    <flux:text class="leading-relaxed">{!! cleanNl2br($generated->achievements_text) !!}</flux:text>
                </li>
            @endif
            @if(notEmpty($generated->motivation_text))
                <li class="space-y-2">
                    <flux:heading level="3">{{ trans('generator.content.motivation.title') }}</flux:heading>
                    <flux:text class="leading-relaxed">{!! cleanNl2br($generated->motivation_text) !!}</flux:text>
                </li>
            @endif
            @if(notEmpty($generated->career_goals))
                <li class="space-y-2">
                    <flux:heading level="3">{{ trans('generator.content.goals.title') }}</flux:heading>
                    <flux:text class="leading-relaxed">{!! cleanNl2br($generated->career_goals) !!}</flux:text>
                </li>
            @endif
            @if(notEmpty($generated->other_details))
                <li class="space-y-2">
                    <flux:heading level="3">{{ trans('generator.content.other.title') }}</flux:heading>
                    <flux:text class="leading-relaxed">{!! cleanNl2br($generated->other_details) !!}</flux:text>
                </li>
            @endif
        </ul>
    </flux:text>
</x-generated.panel>
