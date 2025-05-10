@props(['settings'])
<div class="absolute bottom-16 left-0 right-0 w-full space-y-4">
    <div class="w-full flex justify-center gap-1.5">
        {!! trans('invoice.thanks', [
            'name' => '<span class="font-semibold">'.$settings['name'].'</span>'
        ]) !!}
    </div>
    <div class="flex items-center justify-center gap-4">
        <div class="flex items-center gap-1.5">
            <flux:icon.earth class="size-4 text-neutral-500" />
            <a href="{{ $settings['website'] }}" class="text-lime-600 font-medium">
                {{ $settings['website'] }}
            </a>
        </div>
        <div class="flex items-center gap-1.5">
            <flux:icon.mail class="size-4 text-neutral-500" />
            <a href="mailto:{{ $settings['email'] }}" class="text-lime-600 font-medium">
                {{ $settings['email'] }}
            </a>
        </div>
    </div>
</div>
