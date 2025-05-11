<div class="space-y-6">
    <x-page.header
        :title="trans('privacy.title')"
        icon="shield-alert"
        :description="trans('privacy.description')"
    />
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('privacy.a.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <div class="space-y-2">
            <flux:heading level="3">
                {{ trans('privacy.a1.title') }}
            </flux:heading>
            <flux:text class="space-y-2">
                <p>{{ trans('privacy.a1.text') }}</p>
                <ul class="list-disc pl-4 space-y-1">
                    <li>{{ trans('privacy.a1.text.a') }}</li>
                    <li>{{ trans('privacy.a1.text.b') }}</li>
                    <li>{{ trans('privacy.a1.text.c') }}</li>
                </ul>
            </flux:text>
        </div>
        <div class="space-y-2">
            <flux:heading level="3">
                {{ trans('privacy.a2.title') }}
            </flux:heading>
            <flux:text class="space-y-2">
                <p>{{ trans('privacy.a2.text') }}</p>
                <ul class="list-disc pl-4 space-y-1">
                    <li>{{ trans('privacy.a2.text.a') }}</li>
                    <li>{{ trans('privacy.a2.text.b') }}</li>
                    <li>{{ trans('privacy.a2.text.c') }}</li>
                    <li>{{ trans('privacy.a2.text.d') }}</li>
                    <li>{{ trans('privacy.a2.text.e') }}</li>
                </ul>
            </flux:text>
        </div>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('privacy.b.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text class="space-y-2">
            <p>{{ trans('privacy.b.text') }}</p>
            <ul class="list-disc pl-4 space-y-1">
                <li>{{ trans('privacy.b.text.a') }}</li>
                <li>{{ trans('privacy.b.text.b') }}</li>
                <li>{{ trans('privacy.b.text.c') }}</li>
                <li>{{ trans('privacy.b.text.d') }}</li>
                <li>{{ trans('privacy.b.text.e') }}</li>
                <li>{{ trans('privacy.b.text.f') }}</li>
                <li>{{ trans('privacy.b.text.g') }}</li>
            </ul>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
           {{ trans('privacy.c.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text class="space-y-2">
            <p>{{ trans('privacy.c.text') }}</p>
            <ul class="list-disc pl-4 space-y-1">
                <li>{{ trans('privacy.c.text.a') }}</li>
                <li>{{ trans('privacy.c.text.b') }}</li>
                <li>{{ trans('privacy.c.text.c') }}</li>
            </ul>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('privacy.d.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('privacy.d.text') }}</p>
        </flux:text>
        <div class="space-y-2">
            <flux:heading level="3">
                {{ trans('privacy.d1.title') }}
            </flux:heading>
            <flux:text>
                <p>{{ trans('privacy.d1.text') }}</p>
            </flux:text>
        </div>
        <div class="space-y-2">
            <flux:heading level="3">
                {{ trans('privacy.d2.title') }}
            </flux:heading>
            <flux:text>
                <p>{{ trans('privacy.d2.text') }}</p>
            </flux:text>
        </div>
        <div class="space-y-2">
            <flux:heading level="3">
                {{ trans('privacy.d3.title') }}
            </flux:heading>
            <flux:text>
                <p>{{ trans('privacy.d3.text') }}</p>
            </flux:text>
        </div>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('privacy.e.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text class="space-y-3">
            <p>{{ trans('privacy.e.text.1') }}</p>
            <ul class="list-disc pl-4 space-y-1">
                <li><span class="font-semibold">{{ trans('privacy.e1.title') }}</span>: {{ trans('privacy.e1.text') }}</li>
                <li><span class="font-semibold">{{ trans('privacy.e2.title') }}</span>: {{ trans('privacy.e2.text') }}</li>
                <li><span class="font-semibold">{{ trans('privacy.e3.title') }}</span>: {{ trans('privacy.e3.text') }}</li>
                <li><span class="font-semibold">{{ trans('privacy.e4.title') }}</span>: {{ trans('privacy.e4.text') }}</li>
                <li><span class="font-semibold">{{ trans('privacy.e5.title') }}</span>: {{ trans('privacy.e5.text') }}</li>
                <li><span class="font-semibold">{{ trans('privacy.e6.title') }}</span>: {{ trans('privacy.e6.text') }}</li>
            </ul>
            <p>{{ trans('privacy.e.text.2') }}</p>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('privacy.f.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('privacy.f.text') }}</p>
        </flux:text>
        <div class="space-y-2">
            <flux:heading level="3">
                {{ trans('privacy.f1.title') }}
            </flux:heading>
            <flux:text>
                <p>{{ trans('privacy.f1.text') }}</p>
            </flux:text>
        </div>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('privacy.g.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('privacy.g.text') }}</p>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('privacy.h.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('privacy.h.text') }}</p>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('privacy.i.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('privacy.i.text') }}</p>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('privacy.j.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('privacy.j.text') }}</p>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('privacy.k.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('privacy.k.text', ['email' => $contact]) }}</p>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('privacy.l.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('privacy.l.text', ['date' => '1st April 2025']) }}</p>
        </flux:text>
    </flux:card>
</div>
