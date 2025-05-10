<!DOCTYPE html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    itemscope
    itemtype="https://schema.org/Article"
    class="min-h-screen">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}" />
        <title>{{ $title ?? '' }} | {{ config('app.name') }}</title>
        <x-layouts.template.seo :description="$description ?? ''" />
        <x-layouts.template.favicons />
        <x-layouts.template.socials :$title :description="$description ?? ''" />
        <x-layouts.template.resources />
    </head>
    <body class="min-h-screen bg-zinc-200/60 dark:bg-charcoal antialiased">
        <x-layouts.template.wrapper>
            {{ $slot }}
        </x-layouts.template.wrapper>
        <livewire:contact.index />
        @auth
            <livewire:contact.feedback />
        @endauth
        @persist('toast')
            <flux:toast />
        @endpersist
        @livewireScripts
        @fluxScripts
        @if(app()->isProduction())
            <script data-collect-dnt="true" async src="https://scripts.simpleanalyticscdn.com/latest.js"></script>
        @endif
    </body>
</html>
