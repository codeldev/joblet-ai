{{-- Credit: Lucide (https://lucide.dev) --}}

@props([
    'variant' => 'outline',
])

@php
if ($variant === 'solid') {
    throw new \Exception('The "solid" variant is not supported in Lucide.');
}

$classes = Flux::classes('shrink-0')
    ->add(match($variant) {
        'outline' => '[:where(&)]:size-6',
        'solid' => '[:where(&)]:size-6',
        'mini' => '[:where(&)]:size-5',
        'micro' => '[:where(&)]:size-4',
    });

$strokeWidth = match ($variant) {
    'outline' => 2,
    'mini' => 2.25,
    'micro' => 2.5,
};
@endphp

<svg
    {{ $attributes->class($classes) }}
    data-flux-icon
    xmlns="http://www.w3.org/2000/svg"
    viewBox="0 0 24 24"
    fill="none"
    stroke="currentColor"
    stroke-width="{{ $strokeWidth }}"
    stroke-linecap="round"
    stroke-linejoin="round"
    aria-hidden="true"
    data-slot="icon"
>
  <path d="M12 10a2 2 0 0 0-2 2c0 1.02-.1 2.51-.26 4" />
  <path d="M14 13.12c0 2.38 0 6.38-1 8.88" />
  <path d="M17.29 21.02c.12-.6.43-2.3.5-3.02" />
  <path d="M2 12a10 10 0 0 1 18-6" />
  <path d="M2 16h.01" />
  <path d="M21.8 16c.2-2 .131-5.354 0-6" />
  <path d="M5 19.5C5.5 18 6 15 6 12a6 6 0 0 1 .34-2" />
  <path d="M8.65 22c.21-.66.45-1.32.57-2" />
  <path d="M9 6.8a6 6 0 0 1 9 5.2v2" />
</svg>
