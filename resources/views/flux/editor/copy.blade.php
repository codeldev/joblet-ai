@props(['content'])
<flux:tooltip content="{{ trans('letter.result.actions.copy') }}" class="contents">
    <flux:editor.button x-data x-on:click="$clipboard({
        content : '{{ $content }}',
        success : () => $wire.dispatch('toast-show',
        {
            duration : 1500,
            slots    : { text: '{{ trans('letter.result.actions.copied') }}' },
            dataset  : { variant: 'success', position: 'bottom right' }
        }),
    })">
        <flux:icon.copy class="size-[1.25rem]" />
    </flux:editor.button>
</flux:tooltip>
