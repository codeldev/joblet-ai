@props(['title', 'icon', 'description' => null])
<div class="text-center xs:text-left space-y-2">
    <flux:heading level="1" class="flex items-center justify-center xs:justify-start gap-2">
        <flux:icon :$icon class="size-6" />
        {{ $title }}
    </flux:heading>
    @if($description)
        <flux:text>
            <p>{{ $description }}</p>
        </flux:text>
    @endif
    {{ $slot }}
</div>
