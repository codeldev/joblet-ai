<div class="max-w-sm mx-auto space-y-4">
    <flux:heading level="1" class="flex items-center justify-center gap-2">
        <flux:icon.lock-keyhole class="size-6" />
        {{ trans('auth.lockout.title') }}
    </flux:heading>
    <flux:card class="space-y-4">
        <flux:text class="text-center leading-relaxed">
            {{ trans('auth.lockout.description') }}
        </flux:text>
        <flux:badge
            color="sky"
            class="w-full justify-center h-10"
            wire:poll.keep-alive.5s="lockoutTimer"
        >
            <div class="p-1.5">
                {{ $lockoutMessage }}
            </div>
        </flux:badge>
    </flux:card>
</div>
