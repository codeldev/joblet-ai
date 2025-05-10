<x-home.wrapper>
    <x-home.container class="space-y-12 lg:space-y-16">
        <x-home.header>
            <x-slot:header>
                {{ trans('home.testimonials.title.part1') }}
                <span class="text-indigo-500 dark:text-indigo-300/80">
                    {{ trans('home.testimonials.title.part2') }}
                </span>
            </x-slot:header>
            <x-slot:description>
                {{ trans('home.testimonials.subtitle') }}
            </x-slot:description>
        </x-home.header>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-4 sm:gap-x-6 lg:gap-x-8 xl:gap-x-10 2xl:gap-x-12 gap-y-8 md:gap-y-12">
            @for($i = 1; $i <= 4; $i++)
                <x-home.testimonial
                    :name="trans('fake.testimonial.' .  $i .'.name')"
                    :position="trans('fake.testimonial.' .  $i .'.role')"
                    :comments="trans('fake.testimonial.' .  $i .'.text', ['app' => $appName])"
                />
            @endfor
        </div>
    </x-home.container>
</x-home.wrapper>
