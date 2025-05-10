<div>
    <div class="space-y-4">
        <flux:separator />
        <div class="flex flex-wrap items-center justify-center sm:justify-between gap-2 sm:gap-4">
            <flux:text class="flex flex-col xs:flex-row items-center text-center gap-1.5 !text-zinc-900 dark:!text-white">
                <flux:icon.info class="hidden xs:flex size-4" />
                {{ trans('account.credits.available', [
                    'available' => $this->credits,
                ]) }}
            </flux:text>
            <flux:modal.trigger name="order-credits">
                <flux:link
                    variant="ghost"
                    class="!flex items-center gap-1.5 !font-normal text-sm cursor-pointer"
                >
                    <flux:icon.shopping-basket class="size-4" />
                    {{ trans('account.credits.order.button') }}
                </flux:link>
            </flux:modal.trigger>
        </div>
        <flux:separator />
    </div>
    <flux:modal
        name="order-credits"
        class="max-w-md"
    >
        <flux:heading
            size="lg"
            class="flex items-center gap-2"
        >
            <flux:icon.shopping-basket class="size-5" />
            <span>{{ trans('account.credits.order.title') }}</span>
        </flux:heading>
        <flux:subheading>
            {{ trans("account.credits.order.description") }}
        </flux:subheading>
        <flux:radio.group
            variant="cards"
            :indicator="false"
            wire:model.live="packageId"
            class="flex-col gap-4 mt-6"
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
                        <flux:subheading>
                            {{ $package->description }}
                        </flux:subheading>
                    </div>
                </flux:radio>
            @endforeach
        </flux:radio.group>
    </flux:modal>
</div>
