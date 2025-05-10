@props(['title', 'description' => null])
@if($description)
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $title }}">
    <meta name="twitter:description" content="{{ $description }}">
    <meta name="twitter:image" content="{{ url('assets/img/social/preview.png') }}">
    <meta property="og:title" content="{{ $title }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:image" content="{{ url('assets/img/social/preview.png') }}">
    <meta property="og:description" content="{{ $description }}">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta itemprop="name" content="{{ $title  }}">
    <meta itemprop="description" content="{{ $description }}">
    <meta itemprop="image" content="{{ url('assets/img/social/preview.png') }}">
@endif
