<div>
    <div class="fixed z-20 right-0 top-16 sm:top-20">
        <flux:modal.trigger name="feedback-form">
            <button type="button" class="h-9 w-8 bg-indigo-600 aspect-square rounded-tl-lg rounded-bl-lg text-white flex items-center justify-start pl-2 shadow-lg hover:w-28 opacity-50 hover:opacity-100 ease-in-out duration-150 transition-all cursor-pointer group">
                <flux:icon.message-circle-more class="size-5 flex-shrink-0" />
                <span class="text-sm ml-2 opacity-0 w-0 group-hover:opacity-100 group-hover:w-auto overflow-hidden whitespace-nowrap  ease-in-out transition-all duration-150">
                {{ trans("messages.feedback.trigger") }}
            </span>
            </button>
        </flux:modal.trigger>
    </div>
    <flux:modal
        name="feedback-form"
        class="w-full max-w-xl space-y-5"
        wire:close="close"
        wire:cancel="close"
    >
        <div>
            <flux:heading
                size="xl"
                class="flex items-center gap-2 max-md:!text-lg"
            >
                <flux:icon.message-circle-more class="max-md:size-6 size-8" />
                {{ trans('messages.feedback.title') }}
            </flux:heading>
            <flux:subheading class="leading-relaxed space-y-4">
                {{ trans("messages.feedback.description") }}
            </flux:subheading>
        </div>
        <flux:separator />
        <form wire:submit="submit">
            <div class="space-y-4 mb-6">
                <flux:input
                    type="text"
                    wire:model="form.name"
                    :label="trans('messages.feedback.name.label')"
                    :description="trans('messages.feedback.name.description')"
                />
                <flux:input
                    type="email"
                    wire:model="form.email"
                    :label="trans('messages.feedback.email.label')"
                    :description="trans('messages.feedback.email.description')"
                />
                <flux:textarea
                    wire:model="form.message"
                    class="min-h-[100px]"
                    :label="trans('messages.feedback.message.label')"
                    :description="trans('messages.feedback.message.description')"
                />
            </div>
            <flux:button
                type="submit"
                variant="primary"
                class="h-12 w-full">
                <div class="flex items-center gap-2">
                    <flux:icon.send-horizontal class="size-5" />
                    <span>{{ trans('messages.feedback.submit') }}</span>
                </div>
            </flux:button>
        </form>
    </flux:modal>
</div>
