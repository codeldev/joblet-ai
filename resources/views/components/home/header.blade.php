@props(['description'])
<div {{ $attributes->merge(['class' => 'text-center space-y-4']) }}>
    <h2 class="text-2xl sm:text-3xl lg:text-4xl font-bold tracking-tight text-zinc-900 dark:text-white space-y-4">
        {{ $header }}
    </h2>
    <flux:text>
        <p class="text-sm xs:text-base lg:text-lg max-w-md sm:max-w-lg lg:max-w-xl xl:max-w-5xl mx-auto">
            {{ $description }}
        </p>
    </flux:text>
</div>
