<flux:card class="space-y-6">
    <flux:heading size="lg" class="text-indigo-600 dark:text-indigo-300">
        {{ trans('support.section.privacy.title') }}
    </flux:heading>
    <flux:accordion>
        <flux:accordion.item
            :heading="trans('support.section.privacy.1.question', ['app' => $appName])"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.privacy.1.answer') }}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>

        <flux:accordion.item
            :heading="trans('support.section.privacy.2.question')"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.privacy.2.answer.1') }}</p>
                    <p>{{ trans('support.section.privacy.2.answer.2', ['app' => $appName]) }}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>

        <flux:accordion.item
            :heading="trans('support.section.privacy.3.question')"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.privacy.3.answer.1') }}</p>
                    <p>{{ trans('support.section.privacy.3.answer.2') }}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>

        <flux:accordion.item
            :heading="trans('support.section.privacy.4.question')"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.privacy.4.answer.1') }}</p>
                    <p>{{ trans('support.section.privacy.4.answer.2') }}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</flux:card>
