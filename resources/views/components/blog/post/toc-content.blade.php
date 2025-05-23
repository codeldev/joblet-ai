@props(['post'])
<div
    class="overflow-hidden transition-all duration-300 ease-in-out"
    :style="open ? 'height: ' + contentHeight : 'height: 0px'"
    x-ref="toc"
>
    <flux:card class="p-5 lg:p-6 xl:p-8 dark:shadow-2xl dark:!bg-zinc-900/70 mt-5">
        <ol class="text-sm space-y-3 list-decimal list-inside">
            @foreach($post->markdown_html->toc as $toc)
                <li>
                    <flux:link href="#{{ $toc->id }}" class="!font-normal pl-2">
                        {{ html_entity_decode($toc->text) }}
                    </flux:link>
                    @isset($toc->children)
                        <div class="mt-2">
                            <ul class="list-disc list-inside pl-6 space-y-1">
                                @foreach($toc->children as $child)
                                    <li>
                                        <flux:link href="#{{ $child->id }}" class="!font-normal">
                                            {{ html_entity_decode($child->text) }}
                                        </flux:link>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endisset
                </li>
            @endforeach
        </ol>
    </flux:card>
</div>
