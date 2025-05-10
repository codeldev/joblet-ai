<x-layouts.base :$title :$description>
    <div class="flex flex-col min-h-screen">
        <x-layouts.template.header />
        <x-layouts.template.body :auth="false">
            {{ $slot }}
        </x-layouts.template.body>
        <x-layouts.template.footer />
    </div>
</x-layouts.base>
