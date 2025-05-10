@props(['settings'])
<div class="space-y-4">
    <div class="flex justify-center">
        <div class="!bg-lime-600/70 !text-white font-medium rounded-md h-10 flex items-center justify-center px-3.5">
            {{ trans('invoice.paid') }}
        </div>
    </div>
    <div class="text-center">
        {{ trans('invoice.descriptor') }}
        <span class="inline-flex rounded items-center h-6 px-1.5 bg-neutral-200/50 font-semibold tracking-wider font-mono text-xs">
            {{ $settings['descriptor'] }}
        </span>
    </div>
</div>
