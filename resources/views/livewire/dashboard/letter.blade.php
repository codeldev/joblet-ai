<flux:modal
    name="show-viewable"
    class="w-full max-w-5xl max-xs:p-3 max-md:p-4"
    :dismissible="false"
    wire:close="close"
    wire:cancel="close">
    @if($generated)
        <div class="space-y-6 p-2">
            <div>
                <flux:heading
                    size="xl"
                    class="flex items-center gap-2 max-md:!text-lg"
                >
                    <flux:icon.file-text class="max-md:size-6 size-8" />
                    {{ trans('letter.result.title') }}
                </flux:heading>
                <flux:subheading>
                    {{ trans('letter.result.description') }}
                </flux:subheading>
            </div>

            <flux:editor
                wire:model="generatedContentHtml"
                class="h-full"
            >
                <flux:editor.toolbar>
                    <flux:editor.heading />
                    <flux:editor.separator />
                    <flux:editor.bold />
                    <flux:editor.italic />
                    <flux:editor.strike />
                    <flux:editor.underline />
                    <flux:editor.separator />
                    <flux:editor.bullet />
                    <flux:editor.ordered />
                    <flux:editor.blockquote />
                    <flux:editor.separator />
                    <flux:editor.align />
                    <flux:editor.separator />
                    <flux:editor.subscript />
                    <flux:editor.superscript />
                    <flux:editor.separator />
                    <flux:editor.highlight />
                    <flux:editor.link />
                    <flux:editor.code />
                    <flux:editor.separator />
                    <flux:editor.undo />
                    <flux:editor.redo />
                    <flux:editor.spacer />
                    <flux:editor.download />
                    <flux:editor.separator />
                    <flux:editor.save />
                    <flux:editor.separator />
                    <flux:editor.copy :content="base64_encode($generatedContentText ?? '')" />
                </flux:editor.toolbar>
                <flux:editor.content />
            </flux:editor>
        </div>
    @endif
</flux:modal>
