<x-layouts.base :$title :$description>
    <div class="flex flex-col min-h-screen">
        <x-layouts.template.header />
        <main>{{ $slot }}</main>
        <x-layouts.template.footer />
    </div>
</x-layouts.base>
