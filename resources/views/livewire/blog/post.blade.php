<div class="space-y-6"
     x-data="{ open: false, contentHeight: '0px' }"
     x-init="$nextTick(() => { contentHeight = $refs.toc.scrollHeight + 'px' })"
>
    <x-blog.post.heading :$post />
    <div>
        <flux:separator variant="subtle" />
        <div class="flex items-center flex-col md:flex-row justify-between gap-2 md:gap-4 mt-5">
            <x-blog.post.meta :$post />
            <x-blog.post.toc-trigger />
        </div>
        <x-blog.post.toc-content :$post />
    </div>
    <x-blog.post.image :$post :$disk />
    <x-blog.post.content>
        {!! $post->markdown_html->html !!}
    </x-blog.post.content>
</div>
