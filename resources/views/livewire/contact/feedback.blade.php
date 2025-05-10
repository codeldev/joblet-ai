<div>
    <div class="fixed z-20 right-0 top-16 sm:top-1/5">
        <flux:modal.trigger name="feedback-form">
            <button type="button" class="h-9 w-8 sm:h-12 sm:w-12 bg-indigo-600 aspect-square rounded-tl-lg rounded-bl-lg text-white flex items-center justify-start pl-2 sm:pl-4 shadow-lg hover:w-28 sm:hover:w-32 opacity-50 hover:opacity-100 ease-in-out duration-150 transition-all cursor-pointer group">
                <flux:icon.message-circle-more class="size-5 sm:size-6 flex-shrink-0" />
                <span class="text-sm ml-2 opacity-0 w-0 group-hover:opacity-100 group-hover:w-auto overflow-hidden whitespace-nowrap  ease-in-out transition-all duration-150">
                {{ trans("messages.feedback.trigger") }}
            </span>
            </button>
        </flux:modal.trigger>
    </div>
    <flux:modal
        name="feedback-form"
        class="w-full max-w-lg"
        wire:close="close"
        wire:cancel="close"
    >
        <div class="mb-4">
            <flux:heading
                size="lg"
                class="flex items-center gap-2"
            >
                <flux:icon.message-circle-more class="size-5" />
                <span>{{ trans('messages.feedback.title') }}</span>
            </flux:heading>
            <flux:subheading class="leading-relaxed space-y-4">
                {{ trans('messages.feedback.description') }}
            </flux:subheading>
        </div>
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
