<x-layouts.base :$title>
    <div class="flex flex-col min-h-screen">
        <x-layouts.template.header />
        <x-layouts.template.body :auth="true">
            {{ $slot }}
        </x-layouts.template.body>
        <x-layouts.template.footer />
    </div>
</x-layouts.base>
