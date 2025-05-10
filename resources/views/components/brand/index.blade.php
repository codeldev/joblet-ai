@props(['size' => 'default'])
<div class="flex items-center gap-2">
    @switch($size)
        @case('pdf')
            <x-brand.text class="mt-0.5 h-11 text-zinc-600" />
            <x-brand.icon class="h-11 text-indigo-500" />
        @break
        @case('lg')
            <x-brand.text class="mt-0.5 h-9 xs:h-10 sm:h-12 text-zinc-600 dark:text-white" />
            <x-brand.icon class="h-9 xs:h-10 sm:h-12 text-indigo-500 dark:text-indigo-300/90" />
        @break
        @default
            <x-brand.text class="mt-0.5 h-5 xs:h-6 sm:h-7 text-zinc-600 dark:text-white" />
            <x-brand.icon class="h-5 xs:h-6 sm:h-7 text-indigo-500 dark:text-indigo-300/90" />
        @break
    @endswitch
</div>
