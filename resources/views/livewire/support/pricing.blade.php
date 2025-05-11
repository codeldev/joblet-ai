<flux:card class="space-y-6 !shadow-lg">
    <flux:heading size="lg" class="text-indigo-600 dark:text-indigo-300">
        {{ trans('support.section.account.title') }}
    </flux:heading>
    <flux:accordion>
        <flux:accordion.item
            :heading="trans('support.section.account.1.question', ['app' => $appName])"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.account.1.answer.1') }}</p>
                    <p>{{ trans('support.section.account.1.answer.2') }}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>

        <flux:accordion.item
            :heading="trans('support.section.account.2.question', ['app' => $appName])"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.account.2.answer.1', ['app' => $appName]) }}</p>
                    <p>{{ trans('support.section.account.2.answer.2') }}</p>
                    <div class="space-y-6">
                        <flux:heading level="3">
                            {{ trans('Available packages:') }}
                        </flux:heading>
                        <flux:radio.group
                            variant="cards"
                            :indicator="false"
                            class="flex-col lg:flex-row gap-6"
                        >
                            @foreach($this->packages as $package)
                                <flux:radio
                                    :value="$package->id"
                                    class="cursor-pointer"
                                >
                                    <flux:icon
                                        :icon="$package->meta->icon"
                                        class="size-6"
                                    />
                                    <div class="flex-1 -mt-0.5">
                                        <div class="flex items-center justify-between mb-1">
                                            <flux:heading class="!mb-0 text-base font-semibold">
                                                {{ $package->title }}
                                            </flux:heading>
                                            <flux:badge
                                                size="sm"
                                                :color="$package->meta->color"
                                            >
                                                {{ $package->price->formatted }}
                                            </flux:badge>
                                        </div>
                                        <flux:subheading class="md:mt-2">
                                            {{ $package->description }}
                                        </flux:subheading>
                                    </div>
                                </flux:radio>
                            @endforeach
                        </flux:radio.group>
                    </div>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>

        <flux:accordion.item
            :heading="trans('support.section.account.3.question')"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.account.3.answer') }}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>

        <flux:accordion.item
            :heading="trans('support.section.account.4.question')"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.account.4.answer.1') }}</p>
                    <p>{{ trans('support.section.account.4.answer.2') }}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>

        <flux:accordion.item
            :heading="trans('support.section.account.5.question')"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.account.5.answer') }}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>

        <flux:accordion.item
            :heading="trans('support.section.account.6.question')"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{!! trans('support.section.account.6.answer', ['email' => $contact]) !!}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>

        <flux:accordion.item
            :heading="trans('support.section.account.7.question')"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.account.7.answer') }}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</flux:card>
