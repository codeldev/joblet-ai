<div class="space-y-6">
    <x-page.header
        :title="trans('terms.title')"
        icon="handshake"
        :description="trans('terms.description')"
    />
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('terms.a.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('terms.a.text') }}</p>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('terms.b.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('terms.b.text') }}</p>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('terms.c.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <div class="space-y-2">
            <flux:heading level="3">
                {{ trans('terms.c1.title') }}
            </flux:heading>
            <flux:text>
                <p>{{ trans('terms.c1.text') }}</p>
            </flux:text>
        </div>
        <div class="space-y-2">
            <flux:heading level="3">
                {{ trans('terms.c2.title') }}
            </flux:heading>
            <flux:text>
                <p>{{ trans('terms.c2.text') }}</p>
            </flux:text>
        </div>
        <div class="space-y-2">
            <flux:heading level="3">
                {{ trans('terms.c3.title') }}
            </flux:heading>
            <flux:text>
                <p>{{ trans('terms.c3.text') }}</p>
            </flux:text>
        </div>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('terms.d.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <div class="space-y-2">
            <flux:heading level="3">
                {{ trans('terms.d1.title') }}
            </flux:heading>
            <flux:text>
                <p>{{ trans('terms.d1.text') }}</p>
            </flux:text>
        </div>
        <div class="space-y-2">
            <flux:heading level="3">
                {{ trans('terms.d2.title') }}
            </flux:heading>
            <flux:separator variant="subtle" />
            <flux:text>
                <p>{{ trans('terms.d2.text') }}</p>
            </flux:text>
        </div>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('terms.e.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <div class="space-y-2">
            <flux:heading level="3">
                {{ trans('terms.e1.title') }}
            </flux:heading>
            <flux:text>
                <p>{{ trans('terms.e1.text') }}</p>
            </flux:text>
        </div>
        <div class="space-y-2">
            <flux:heading level="3">
                {{ trans('terms.e2.title') }}
            </flux:heading>
            <flux:text>
                <p>{{ trans('terms.e2.text') }}</p>
            </flux:text>
        </div>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-2 !shadow-lg">
        <flux:heading level="2">
            {{ trans('terms.f.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('terms.f.text') }}</p>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('terms.g.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text class="space-y-4">
            <p>{{ trans('terms.g.text1') }}</p>
            <p>{{ trans('terms.g.text2') }}</p>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('terms.h.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text class="space-y-4">
            <p>{{ trans('terms.h.text') }}</p>
            <ol class="list-disc pl-4 space-y-1">
                <li>{{ trans('terms.h.text.a') }}</li>
                <li>{{ trans('terms.h.text.b') }}</li>
                <li>{{ trans('terms.h.text.c') }}</li>
                <li>{{ trans('terms.h.text.d') }}</li>
            </ol>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('terms.i.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('terms.i.text') }}</p>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('terms.j.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('terms.j.text') }}</p>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('terms.k.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('terms.k.text') }}</p>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('terms.l.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('terms.l.text') }}</p>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('terms.m.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('terms.m.text', ['email' => $contact]) }}</p>
        </flux:text>
    </flux:card>
    <flux:card class="p-5 sm:p-6 space-y-4 !shadow-lg">
        <flux:heading level="2">
            {{ trans('terms.n.title') }}
        </flux:heading>
        <flux:separator variant="subtle" />
        <flux:text>
            <p>{{ trans('terms.n.text', ['date' => '1st April 2025']) }}</p>
        </flux:text>
    </flux:card>
</div>
