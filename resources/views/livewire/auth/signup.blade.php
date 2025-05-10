<form wire:submit="submit" class="space-y-6">
    <div class="space-y-3">
        <flux:input
            type="text"
            wire:model="form.name"
            :label="trans('auth.sign.up.name')"
        />
        <flux:input
            type="email"
            wire:model="form.email"
            :label="trans('auth.sign.up.email')"
        />
        <flux:input
            type="password"
            wire:model="form.password"
            :label="trans('auth.sign.up.password')"
            viewable
        />

        <flux:field variant="inline" class="!flex justify-center xs:justify-start">
            <flux:checkbox wire:model="form.agreed" value="1" />
            <flux:label class="!font-normal !gap-1.5">
                {{ trans('auth.sign.up.agree.terms') }}
                <flux:link :href="route('terms')" variant="ghost" external>
                    {{ trans('auth.sign.up.agree.text') }}
                </flux:link>
            </flux:label>
            <flux:error name="agreed" />
        </flux:field>
    </div>
    <flux:button
        type="submit"
        variant="primary"
        class="w-full h-11"
        icon="user-round-plus"
    >
        {{ trans('auth.sign.up.submit') }}
    </flux:button>
</form>
