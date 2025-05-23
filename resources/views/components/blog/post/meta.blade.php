@props(['post'])
<div class="flex items-center justify-between text-sm text-zinc-600 dark:text-zinc-400 gap-4">
    <time datetime="{{ $post->published_at->format('Y-m-d') }}" class="flex items-center gap-1">
        <flux:icon.calendar class="size-4" />
        {{ $post->published_at->format('M d, Y') }}
    </time>
    <span class="flex items-center gap-1">
        <flux:icon.clock class="size-4" />
        {{ trans('blog.post.read', ['mins' => $post->read_time]) }}
    </span>
</div>
