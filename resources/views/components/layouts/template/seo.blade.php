@props(['description' => null])
@if($description)
    <meta name="description" content="{{ $description }}">
    <meta name="robots" content="index, follow" />
    <meta name="robots" content="noodp, noydir" />
    <link rel="canonical" href="{{ url()->current() }}" />
@endif
