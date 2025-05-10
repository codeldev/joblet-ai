<flux:card class="space-y-6">
    <flux:heading size="lg" class="text-indigo-600 dark:text-indigo-300">
        {{ trans('support.section.general.title') }}
    </flux:heading>
    <flux:accordion>
        <flux:accordion.item
            :heading="trans('support.section.general.1.question', ['app' => $appName])"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.general.1.answer', ['app' => $appName]) }}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>
        <flux:accordion.item
            :heading="trans('support.section.general.2.question', ['app' => $appName])"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.general.2.answer.1') }}</p>
                    <p>{{ trans('support.section.general.2.answer.2') }}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>
        <flux:accordion.item
            :heading="trans('support.section.general.3.question')"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.general.3.answer.1') }}</p>
                    <p>{{ trans('support.section.general.3.answer.2') }}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>
        <flux:accordion.item
            :heading="trans('support.section.general.4.question')"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{!! trans('support.section.general.4.answer', ['email' => $contact]) !!}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</flux:card>
