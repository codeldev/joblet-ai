@props(['user'])
<div class="space-y-3 border border-neutral-300 rounded-lg p-4">
    <h2 class="font-semibold">
        {{ trans('invoice.to') }}
    </h2>
    <div class="space-y-2">
        <div class="flex items-center gap-1.5">
            <flux:icon.user-round class="size-4 text-neutral-500" />
            <div>{{ $user['name'] }}</div>
        </div>
        <div class="flex items-center gap-1.5">
            <flux:icon.mail class="size-4 text-neutral-500" />
            <div>{{ $user['email'] }}</div>
        </div>
    </div>
</div>
