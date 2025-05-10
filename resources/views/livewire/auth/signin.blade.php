<form wire:submit="submit" class="space-y-6">
    <div class="space-y-3">
        <flux:input
            type="email"
            wire:model="form.email"
            :label="trans('auth.sign.in.email')"
        />
        <flux:input
            type="password"
            wire:model="form.password"
            :label="trans('auth.sign.in.password')"
            viewable
        />
    </div>

    <flux:field variant="inline" class="!flex justify-center xs:justify-start">
        <flux:checkbox wire:model="form.remember" value="1" />
        <flux:label class="!font-normal">
            {{ trans('auth.sign.in.remember') }}
        </flux:label>
        <flux:error name="form.remember" />
    </flux:field>
    <flux:button
        type="submit"
        variant="primary"
        class="w-full h-11"
        icon="fingerprint"
    >
        {{ trans('auth.sign.in.submit') }}
    </flux:button>
    <div class="flex flex-col xs:flex-row items-center justify-center gap-2">
        <flux:text class="flex items-center gap-1 font-semibold">
            <flux:icon.wand-sparkles class="size-4" />
            {{ trans('auth.sign.in.forgot') }}
        </flux:text>
        <flux:link
            variant="ghost"
            class="!font-normal text-sm cursor-pointer"
            wire:click="magicLink"
        >
            {{ trans('auth.sign.in.forgot.link') }}
        </flux:link>
    </div>
</form>
