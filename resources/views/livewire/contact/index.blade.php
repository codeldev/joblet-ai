<flux:modal
    name="contact-form"
    class="w-full max-w-lg"
    wire:close="close"
    wire:cancel="close"
    wire:init="onLoad"
>
    <div class="mb-4">
        <flux:heading
            size="lg"
            class="flex items-center gap-2"
        >
            <flux:icon.mail class="size-5" />
            <span>{{ trans('messages.contact.title') }}</span>
        </flux:heading>
        <flux:subheading class="leading-relaxed">
            {{ trans("messages.contact.description") }}
        </flux:subheading>
    </div>

    <form wire:submit="submit">
        <div class="space-y-4 mb-6">
            <flux:input
                type="text"
                wire:model="form.name"
                :label="trans('messages.contact.name.label')"
                :description="trans('messages.contact.name.description')"
            />
            <flux:input
                type="email"
                wire:model="form.email"
                :label="trans('messages.contact.email.label')"
                :description="trans('messages.contact.email.description')"
            />
            <flux:textarea
                wire:model="form.message"
                class="min-h-[100px]"
                :label="trans('messages.contact.message.label')"
                :description="trans('messages.contact.message.description')"
            />
        </div>
        <flux:button
            type="submit"
            variant="primary"
            class="h-12 w-full">
            <div class="flex items-center gap-2">
                <flux:icon.send-horizontal class="size-5" />
                <span>{{ trans("messages.contact.submit") }}</span>
            </div>
        </flux:button>
    </form>
</flux:modal>
