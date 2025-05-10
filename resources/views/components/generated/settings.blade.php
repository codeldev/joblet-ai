@props(['generation'])
<x-generated.toggle-container
    :title="trans('dashboard.generated.settings.title')"
    target="showSettings"
>
    <li>
        {{ trans('dashboard.generated.settings.language', [
            'language' => $generation->language_variant->label()
        ]) }}
    </li>
    <li>
        {{ trans('dashboard.generated.settings.formatting', [
            'format' => $generation->date_format->format()
        ]) }}
    </li>
    <li>
        {{ trans('dashboard.generated.settings.creativity', [
            'creativity' => $generation->option_creativity->label()
        ]) }}
    </li>
    <li>
        {{ trans('dashboard.generated.settings.tone', [
            'tone' => $generation->option_tone->label()
        ]) }}
    </li>
    <li>
        {{ trans('dashboard.generated.settings.length', [
            'length' => $generation->option_length->label()
        ]) }}
    </li>
</x-generated.toggle-container>
