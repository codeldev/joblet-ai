@props(['tab'])
<div {{ $attributes->merge(['class' => 'w-full']) }} x-show="$wire.tab === '{{ $tab }}'" x-cloak>
    {{ $slot }}
</div>
