@props(['generation'])
<div class="w-full flex flex-col xs:flex-row xs:items-center gap-3 xs:gap-4">
    <flux:badge
        size="sm"
        color="zinc"
        icon="calendar-sync"
        class="h-10 xs:h-8 sm:h-10 px-4 xs:px-3 sm:px-4 justify-center !text-sm xs:!text-xs sm:!text-sm !bg-zinc-100 dark:!bg-zinc-900/40"
    >
        {{ trans('dashboard.generated.date.created', [
            'date' => $generation->created_at->format('d/m/Y')
        ]) }}
    </flux:badge>
    <flux:badge
        size="sm"
        color="blue"
        icon="calendar-heart"
        class="h-10 xs:h-8 sm:h-10 px-4 xs:px-3 sm:px-4 justify-center !text-sm xs:!text-xs sm:!text-sm bg-sky-500 text-white"
    >
        {{ trans('dashboard.generated.date.leaving', [
            'date' => $generation->leave_date->format('d/m/Y')
        ]) }}
    </flux:badge>
</div>
