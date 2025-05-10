@props(['size' => 'default'])
@switch($size)
    @case('pdf')
        <div class="flex items-center gap-2">
            <x-brand.part-1 class="h-9 text-zinc-700" />
            <x-brand..part-2 class="h-10 text-indigo-600" />
            <x-brand..part-3 class="h-9 text-zinc-700" />
        </div>
    @break
    @case('lg')
        <div class="flex items-center gap-1.5 dark:gap-2">
            <x-brand.part-1 class="h-8 sm:h-10 text-zinc-700 dark:text-white" />
            <x-brand..part-2 class="h-9 sm:h-11 text-indigo-600 dark:text-indigo-300/90" />
            <x-brand..part-3 class="h-8 sm:h-10 text-zinc-700 dark:text-white" />
        </div>
    @break
    @default
        <div class="flex items-center gap-1 dark:gap-1.5">
            <x-brand.part-1 class="h-4 sm:h-5 text-zinc-700 dark:text-white" />
            <x-brand..part-2 class="h-5 sm:h-6 text-indigo-600 dark:text-indigo-300/90" />
            <x-brand..part-3 class="h-4 sm:h-5 text-zinc-700 dark:text-white" />
        </div>
    @break
@endswitch
