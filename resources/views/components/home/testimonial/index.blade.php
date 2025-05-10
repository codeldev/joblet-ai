@props(['comments', 'name', 'position'])
<flux:card class="relative space-y-6 p-4 sm:p-6 pt-8 sm:pt-12">
    <div class="absolute top-4 xs:top-0 left-4 xs:left-1/2 xs:-translate-x-1/2 xs:-translate-y-1/2 transform">
        <div class="inline-flex size-10 xs:size-12 items-center justify-center rounded-lg aspect-square bg-indigo-100 dark:bg-indigo-500">
            <flux:icon.message-circle-heart class="size-6 xs:size-8 text-indigo-500 dark:text-indigo-200" />
        </div>
    </div>
    <div class="space-y-4">
        <div class="flex justify-center">
            <div class="flex items-center justify-center gap-1">
                @for($i = 1; $i <= 5; $i++)
                    <flux:icon.star class="size-5 text-indigo-500 dark:text-indigo-300/70" />
                @endfor
            </div>
        </div>
        <flux:text class="space-y-6">
            <p class="leading-relaxed text-center md:text-left">
                {{ $comments }}
            </p>
            <div class="text-center md:text-left">
                <p class="text-base font-medium text-zinc-900 dark:text-white">
                    {{ $name }}
                </p>
                <p>
                    {{ $position }}
                </p>
            </div>
        </flux:text>
    </div>
</flux:card>
