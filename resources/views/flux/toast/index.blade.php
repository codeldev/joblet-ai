@php
    $classes = Flux::classes()->add('m-0 p-6 bg-transparent fixed top-24 right-0 left-0 mx-auto');
@endphp
<ui-toast
    x-data
    x-on:toast-show.document="$el.showToast($event.detail)"
    wire:ignore
>
    <template>
        <div
            popover="manual"
            {{ $attributes->class($classes)->only(['class']) }}
            data-position="bottom right"
            data-variant=""
            data-flux-toast-dialog
        >
            <div class="max-w-xl p-2 rounded-xl shadow dark:shadow-xl bg-white dark:border

        [[data-flux-toast-dialog][data-variant=danger]_&]:bg-rose-500
        [[data-flux-toast-dialog][data-variant=info]_&]:bg-sky-500
        [[data-flux-toast-dialog][data-variant=success]_&]:bg-lime-500
        [[data-flux-toast-dialog][data-variant=warning]_&]:bg-amber-500

        [[data-flux-toast-dialog][data-variant=danger]_&]:dark:bg-charcoal
        [[data-flux-toast-dialog][data-variant=info]_&]:dark:bg-charcoal
        [[data-flux-toast-dialog][data-variant=success]_&]:dark:bg-charcoal
        [[data-flux-toast-dialog][data-variant=warning]_&]:dark:bg-charcoal

        [[data-flux-toast-dialog][data-variant=warning]_&]:dark:border-amber-300/80
        [[data-flux-toast-dialog][data-variant=danger]_&]:dark:border-rose-400
        [[data-flux-toast-dialog][data-variant=info]_&]:dark:border-sky-300/80
        [[data-flux-toast-dialog][data-variant=success]_&]:dark:border-lime-300/80
    "
            >
                <div class="flex items-start gap-4">
                    <div class="flex-1 py-1.5 px-2 flex gap-2">
                        <flux:icon.circle-check-big
                            class="hidden [[data-flux-toast-dialog][data-variant=success]_&]:block shrink-0 size-4 mt-0.5 text-white dark:text-lime-300/80"
                        />
                        <flux:icon.info
                            class="hidden [[data-flux-toast-dialog][data-variant=info]_&]:block shrink-0 size-4 mt-0.5 text-white dark:text-sky-300/80"
                        />
                        <flux:icon.bell-ring
                            class="hidden [[data-flux-toast-dialog][data-variant=warning]_&]:block shrink-0 size-4 mt-0.5 text-white dark:text-amber-300/80"
                        />
                        <flux:icon.triangle-alert
                            class="hidden [[data-flux-toast-dialog][data-variant=danger]_&]:block shrink-0 size-4 mt-0.5 text-white dark:text-rose-400"
                        />
                        <div class="font-medium text-sm text-white
                         [[data-flux-toast-dialog][data-variant=warning]_&]:dark:text-amber-300
                         [[data-flux-toast-dialog][data-variant=danger]_&]:dark:text-rose-400 [[data-flux-toast-dialog][data-variant=info]_&]:dark:text-sky-300
                         [[data-flux-toast-dialog][data-variant=success]_&]:dark:text-lime-300"
                        >
                            <slot name="text"></slot>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</ui-toast>
