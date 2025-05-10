<header class="sticky z-20 top-0 px-4 sm:px-6 py-3 sm:py-4 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800">
    <div class="max-w-5xl mx-auto flex items-center justify-between gap-4">
        <a
            href="{{ route('home') }}"
            data-pan="home"
            wire:navigate
        >
            <x-brand />
        </a>
        <nav class="flex items-center gap-6 lg:gap-8">
            @auth
                <flux:button
                    href="{{ route('dashboard') }}"
                    variant="ghost"
                    size="sm"
                    class="!px-2 !font-normal"
                    inset="top bottom left right"
                    icon="layout-dashboard"
                    data-pan="dashboard"
                    wire:navigate
                >
                    <div class="hidden xs:inline">
                        {{ trans('dashboard.menu') }}
                    </div>
                </flux:button>
            @endauth
            <flux:button
                href="{{ route('generator') }}"
                variant="ghost"
                inset="top bottom left right"
                class="!px-2 !font-normal"
                size="sm"
                data-pan="generator"
                icon="file-text"
                wire:navigate
            >
                {{ trans('generator.menu') }}
            </flux:button>
            @auth
                <flux:button
                    variant="ghost"
                    icon="user-round"
                    size="sm"
                    class="!px-2 !font-normal"
                    href="{{ route('account') }}"
                    inset="top bottom left right"
                    data-pan="account"
                    wire:navigate
                >
                    <div class="hidden sm:inline">
                        {{ trans('account.menu') }}
                    </div>
                </flux:button>
            @endauth
            @guest
                <flux:button
                    variant="ghost"
                    size="sm"
                    class="!px-2 !font-normal"
                    icon="user-round"
                    href="{{ route('auth') }}"
                    data-pan="auth"
                    inset="top bottom left right"
                    wire:navigate
                >
                    <div class="hidden sm:inline">
                        {{ trans('auth.menu') }}
                    </div>
                </flux:button>
            @endguest
            <flux:modal.trigger name="contact-form">
                <flux:button
                    variant="ghost"
                    inset="top bottom left right"
                    class="!px-2 !font-normal"
                    size="sm"
                    data-pan="contact"
                    icon="mail"
                >
                    <div class="hidden sm:inline">
                        {{ trans("messages.contact.trigger") }}
                    </div>
                </flux:button>
            </flux:modal.trigger>
            <flux:button
                x-data
                x-on:click="$flux.dark = ! $flux.dark"
                variant="ghost"
                size="sm"
                aria-label="{{ trans('misc.dark.mode') }}"
                :tooltip="trans('misc.dark.mode')"
                data-pan="darkmode"
                inset="left right"
                square
            >
                <flux:icon.moon class="size-5" x-show="$flux.dark" x-cloak />
                <flux:icon.sun class="size-6" x-show="!$flux.dark" x-cloak />
            </flux:button>
        </nav>
    </div>
</header>
