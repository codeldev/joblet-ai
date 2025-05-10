<x-home.wrapper>
    <x-home.container class="space-y-8 md:space-y-12 lg:space-y-16">
        <x-home.header>
            <x-slot:header>
                {{ trans('home.faq.title.part1') }}
                <span class="text-indigo-500 dark:text-indigo-300/80">
                    {{ trans('home.faq.title.part2') }}
                </span>
                {{ trans('home.faq.title.part3') }}
            </x-slot:header>
            <x-slot:description>
                {{ trans('home.faq.subtitle') }}
            </x-slot:description>
        </x-home.header>

        <flux:accordion class="mx-auto max-w-3xl">
            <x-home.faq
                :expanded="true"
                :question="trans('support.section.general.1.question', ['app' => $appName])"
            >
                <p>{{ trans('support.section.general.1.answer', ['app' => $appName]) }}</p>
            </x-home.faq>
            <x-home.faq
                :expanded="true"
                :question="trans('support.section.general.2.question', ['app' => $appName])"
            >
                <p>{{ trans('support.section.general.2.answer.1') }}</p>
                <p>{{ trans('support.section.general.2.answer.2') }}</p>
            </x-home.faq>
            <x-home.faq :question="trans('support.section.account.1.question', ['app' => $appName])">
                <p>{{ trans('support.section.account.1.answer.1') }}</p>
                <p>{{ trans('support.section.account.1.answer.2') }}</p>
            </x-home.faq>
            <x-home.faq :question="trans('support.section.account.3.question')">
                <p>{{ trans('support.section.account.3.answer') }}</p>
            </x-home.faq>
            <x-home.faq :question="trans('support.section.generation.1.question')">
                <p>{{ trans('support.section.generation.1.answer.1') }}</p>
                <ul class="list-disc pl-5">
                    <li>{{ trans('support.section.generation.1.answer.list.1') }}</li>
                    <li>{{ trans('support.section.generation.1.answer.list.2') }}</li>
                    <li>{{ trans('support.section.generation.1.answer.list.3') }}</li>
                    <li>{{ trans('support.section.generation.1.answer.list.4') }}</li>
                    <li>{{ trans('support.section.generation.1.answer.list.5') }}</li>
                </ul>
                <p>{{ trans('support.section.generation.1.answer.2') }}</p>
            </x-home.faq>
            <x-home.faq :question="trans('support.section.generation.2.question')">
                <p>{{ trans('support.section.generation.2.answer.1', ['app' => $appName]) }}</p>
                <ul class="list-disc pl-5">
                    <li>{{ trans('support.section.generation.2.answer.list.1') }}</li>
                    <li>{{ trans('support.section.generation.2.answer.list.2') }}</li>
                    <li>{{ trans('support.section.generation.2.answer.list.3') }}</li>
                    <li>{{ trans('support.section.generation.2.answer.list.4') }}</li>
                    <li>{{ trans('support.section.generation.2.answer.list.5') }}</li>
                </ul>
            </x-home.faq>
            <x-home.faq :question="trans('support.section.generation.3.question')">
                <p>{{ trans('support.section.generation.3.answer.1') }}</p>
                <ul class="list-disc pl-5">
                    <li>{{ trans('support.section.generation.3.answer.list.1') }}</li>
                    <li>{{ trans('support.section.generation.3.answer.list.2') }}</li>
                    <li>{{ trans('support.section.generation.3.answer.list.3') }}</li>
                    <li>{{ trans('support.section.generation.3.answer.list.4') }}</li>
                    <li>{{ trans('support.section.generation.3.answer.list.5') }}</li>
                </ul>
            </x-home.faq>
        </flux:accordion>

        <div class="flex flex-col items-center justify-center xs:flex-row gap-4 sm:gap-8">
            <flux:button
                :href="route('support')"
                variant="primary"
                class="h-12 w-full xs:w-auto"
                wire:navigate
            >
                <flux:icon.circle-help class="size-5" />
                <span>{{ trans('home.faq.cta.button1') }}</span>
            </flux:button>
            <flux:modal.trigger name="contact-form">
                <flux:button
                    variant="outline"
                    class="h-12 w-full xs:w-auto"
                >
                    <flux:icon.message-circle class="size-5" />
                    <span>{{ trans('home.faq.cta.button2') }}</span>
                </flux:button>
            </flux:modal.trigger>
        </div>
    </x-home.container>
</x-home.wrapper>
