<x-home.wrapper id="benefits">
    <x-home.container class="space-y-8 md:space-y-12 lg:space-y-16">
        <x-home.header>
            <x-slot:header>
                {{ trans('home.benefits.title.1') }}
                <span class="text-indigo-500 dark:text-indigo-300/80">
                    {{ trans('home.benefits.title.2') }}
                </span>
                {{ trans('home.benefits.title.3', ['app' => $appName]) }}
            </x-slot:header>
            <x-slot:description>
                {{ trans('home.benefits.subtitle') }}
            </x-slot:description>
        </x-home.header>
        <div class="grid gap-4 sm:gap-6 lg:gap-8 xl:gap-10 2xl:gap-12 grid-cols-1 md:grid-cols-2">
            <x-home.feature
                :title="trans('home.benefits.list.1.title')"
                :description="trans('home.benefits.list.1.text')"
                icon="signature"
            />
            <x-home.feature
                :title="trans('home.benefits.list.2.title')"
                :description="trans('home.benefits.list.2.text')"
                icon="adjustments-horizontal"
            />
            <x-home.feature
                :title="trans('home.benefits.list.3.title')"
                :description="trans('home.benefits.list.3.text')"
                icon="clock-fading"
            />
            <x-home.feature
                :title="trans('home.benefits.list.4.title')"
                :description="trans('home.benefits.list.4.text')"
                icon="lock-keyhole"
            />
        </div>
    </x-home.container>
</x-home.wrapper>
