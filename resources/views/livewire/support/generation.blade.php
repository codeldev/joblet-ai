<flux:card class="space-y-6 !shadow-lg">
    <flux:heading size="lg" class="text-indigo-600 dark:text-indigo-300">
        {{ trans('support.section.generation.title') }}
    </flux:heading>
    <flux:accordion>
        <flux:accordion.item
            :heading="trans('support.section.generation.1.question')"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.generation.1.answer.1') }}</p>
                    <ul class="list-disc pl-5">
                        <li>{{ trans('support.section.generation.1.answer.list.1') }}</li>
                        <li>{{ trans('support.section.generation.1.answer.list.2') }}</li>
                        <li>{{ trans('support.section.generation.1.answer.list.3') }}</li>
                        <li>{{ trans('support.section.generation.1.answer.list.4') }}</li>
                        <li>{{ trans('support.section.generation.1.answer.list.5') }}</li>
                    </ul>
                    <p>{{ trans('support.section.generation.1.answer.2') }}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>

        <flux:accordion.item
            :heading="trans('support.section.generation.2.question')"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.generation.2.answer.1', ['app' => $appName]) }}</p>
                    <ul class="list-disc pl-5">
                        <li>{{ trans('support.section.generation.2.answer.list.1') }}</li>
                        <li>{{ trans('support.section.generation.2.answer.list.2') }}</li>
                        <li>{{ trans('support.section.generation.2.answer.list.3') }}</li>
                        <li>{{ trans('support.section.generation.2.answer.list.4') }}</li>
                        <li>{{ trans('support.section.generation.2.answer.list.5') }}</li>
                    </ul>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>

        <flux:accordion.item
            :heading="trans('support.section.generation.3.question')"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.generation.3.answer.1') }}</p>
                    <ul class="list-disc pl-5">
                        <li>{{ trans('support.section.generation.3.answer.list.1') }}</li>
                        <li>{{ trans('support.section.generation.3.answer.list.2') }}</li>
                        <li>{{ trans('support.section.generation.3.answer.list.3') }}</li>
                        <li>{{ trans('support.section.generation.3.answer.list.4') }}</li>
                        <li>{{ trans('support.section.generation.3.answer.list.5') }}</li>
                    </ul>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>

        <flux:accordion.item
            :heading="trans('support.section.generation.4.question')"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.generation.4.answer.1') }}</p>
                    <ul class="list-disc pl-5">
                        <li>{{ trans('support.section.generation.4.answer.list.1') }}</li>
                        <li>{{ trans('support.section.generation.4.answer.list.2') }}</li>
                        <li>{{ trans('support.section.generation.4.answer.list.3') }}</li>
                        <li>{{ trans('support.section.generation.4.answer.list.4') }}</li>
                        <li>{{ trans('support.section.generation.4.answer.list.5') }}</li>
                    </ul>
                    <p>{{ trans('support.section.generation.4.answer.2') }}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>

        <flux:accordion.item
            :heading="trans('support.section.generation.5.question')"
        >
            <flux:accordion.content>
                <flux:card class="leading-relaxed space-y-4">
                    <p>{{ trans('support.section.generation.5.answer.1') }}</p>
                    <ul class="list-disc pl-5">
                        <li>{{ trans('support.section.generation.5.answer.list.1') }}</li>
                        <li>{{ trans('support.section.generation.5.answer.list.2') }}</li>
                        <li>{{ trans('support.section.generation.5.answer.list.3') }}</li>
                        <li>{{ trans('support.section.generation.5.answer.list.4') }}</li>
                    </ul>
                    <p>{{ trans('support.section.generation.5.answer.2') }}</p>
                </flux:card>
            </flux:accordion.content>
        </flux:accordion.item>
    </flux:accordion>
</flux:card>
