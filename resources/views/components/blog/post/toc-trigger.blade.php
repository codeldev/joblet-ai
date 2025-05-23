<button
    type="button"
    x-on:click="open = !open"
    class="flex items-center gap-2 cursor-pointer text-sm text-sky-600 dark:text-sky-300"
>
    <flux:icon.info class="size-4" />
    <span>
        <span class="hidden xs:inline">
            {{ trans('blog.toc.start') }}
        </span>
        {{ trans('blog.toc.end') }}
    </span>
</button>
