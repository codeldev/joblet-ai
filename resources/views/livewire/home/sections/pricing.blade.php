<x-home.wrapper class="bg-white dark:bg-charcoal border-y border-zinc-200 dark:border-zinc-700/60">
    <x-home.container class="space-y-8 md:space-y-12 lg:space-y-16">
        <x-home.header>
            <x-slot:header>
                {{ trans('home.pricing.title.part1') }},
                <span class="text-indigo-500 dark:text-indigo-300/80">
                    {{ trans('home.pricing.title.part2') }}
                </span>
                {{ trans('home.pricing.title.part3') }}
            </x-slot:header>
            <x-slot:description>
                {{ trans('home.pricing.subtitle') }}
            </x-slot:description>
        </x-home.header>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 lg:gap-8 xl:gap-10 2xl:gap-12">
            @foreach($packages as $package)
                <x-home.package
                    class="{{ $loop->iteration === 1  ? 'col-span-1 md:col-span-2 lg:col-span-1' : '' }}"
                    :title="$package->title"
                    :description="$package->subtitle"
                    :price="$package->price->formatted"
                    :label="$package->frequency"
                    :button="$loop->iteration === 1 ? 'Sign Up Free' : 'Sign in to continue'"
                    :icon="$loop->iteration === 1 ? 'user-round-plus' : 'fingerprint'"
                    :selected="$loop->iteration === 2"
                >
                    <x-slot:list>
                        @foreach($package->benefits as $text)
                            <x-home.package.bullet :$text />
                        @endforeach
                    </x-slot:list>
                </x-home.package>
            @endforeach
        </div>
    </x-home.container>
</x-home.wrapper>
