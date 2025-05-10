@props(['settings'])
<div class="space-y-3">
    <x-brand size="pdf" />
    <div class="text-sm leading-normal">
        @foreach($settings['address'] as $addressLine)
            <p>{{ $addressLine }}</p>
        @endforeach
    </div>
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-1.5">
            <flux:icon.earth class="size-4 text-neutral-500" />
            <a href="{{ $settings['website'] }}" class="text-indigo-600 font-medium">
                {{ $settings['website'] }}
            </a>
        </div>
        <div class="flex items-center gap-1.5">
            <flux:icon.mail class="size-4 text-neutral-500" />
            <a href="mailto:{{ $settings['email'] }}" class="text-indigo-600 font-medium">
                {{ $settings['email'] }}
            </a>
        </div>
    </div>
</div>
