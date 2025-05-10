<footer class="p-6 bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800">
    <div class="max-w-5xl mx-auto flex flex-col lg:flex-row items-center justify-between gap-4 lg:gap-6">
        <div class="flex items-center gap-1">
            <flux:icon.copyright class="size-4" />
            <flux:text>
                {{ trans('misc.footer.copyright', [
                    'name' => config('app.name'),
                    'year' => now()->year,
                ]) }}
            </flux:text>
        </div>
        <nav class="grid grid-cols-2 sm:flex items-center justify-center gap-4">
            <flux:link
                :href="route('generator')"
                variant="ghost"
                class="!flex items-center gap-1 !font-normal text-sm"
                data-pan="generator"
                wire:navigate
            >
                <flux:icon.file-text class="size-4" />
                {{ trans('generator.menu') }}
            </flux:link>

            <flux:link
                :href="route('terms')"
                variant="ghost"
                data-pan="terms"
                class="!flex items-center gap-1 !font-normal text-sm"
                wire:navigate
            >
                <flux:icon.handshake class="size-4" />
                {{ trans('terms.menu') }}
            </flux:link>

            <flux:link
                :href="route('privacy')"
                class="!flex items-center gap-1 !font-normal text-sm"
                variant="ghost"
                data-pan="privacy"
                wire:navigate
            >
                <flux:icon.shield-alert class="size-4" />
                {{ trans('privacy.menu') }}
            </flux:link>
            <flux:link
                :href="route('support')"
                class="!flex items-center gap-1 !font-normal text-sm"
                variant="ghost"
                data-pan="support"
                wire:navigate
            >
                <flux:icon.life-buoy class="size-4" />
                {{ trans('support.menu') }}
            </flux:link>
        </nav>
    </div>
</footer>
