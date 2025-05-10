@props(['auth' => false])
<main @class([
    'flex-1 p-5 md:p-6 lg:p-8',
    'flex flex-col h-full' => $auth
])>
    <div @class([
        'max-w-5xl mx-auto',
        'flex-1 flex flex-col h-full' => $auth
    ])>
        {{ $slot }}
    </div>
</main>
