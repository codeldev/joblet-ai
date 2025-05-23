@props(['disk', 'post'])
<article class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg dark:shadow-2xl overflow-hidden border border-zinc-200 dark:border-zinc-700/60 hover:scale-105 transition-transform duration-200">
    <a href="{{ route('resources.post', $post) }}">
        <div class="relative aspect-video overflow-hidden">
            <x-blog.featured-image :$post :$disk />
        </div>
        <div class="p-5 space-y-4">
            <div class="flex items-center justify-between text-sm text-zinc-500 dark:text-zinc-400 gap-4">
                <time datetime="{{ $post->published_at->format('Y-m-d') }}" class="flex items-center gap-1">
                    <flux:icon.calendar class="size-4" />
                    {{ $post->published_at->format('M d, Y') }}
                </time>
                <span class="flex items-center gap-1">
                    <flux:icon.clock class="size-4" />
                    {{ trans('blog.post.read', ['mins' => $post->read_time]) }}
                </span>
            </div>
            <div class="space-y-3">
                <h2 class="text-sm font-semibold text-sky-600 dark:text-sky-400 line-clamp-2 leading-relaxed dark:text-shadow">
                    {{ $post->title }}
                </h2>
                <flux:text>
                    <p class="line-clamp-4 leading-relaxed">
                        {{ $post->summary }}
                    </p>
                </flux:text>
            </div>
        </div>
    </a>
</article>
