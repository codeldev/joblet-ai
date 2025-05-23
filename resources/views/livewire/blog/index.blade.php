<div class="space-y-8">
    <x-page.header
        :title="trans('blog.posts.title')"
        icon="book-open"
        :description="trans('blog.posts.description')"
    />
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-6 lg:gap-8">
        @foreach($posts as $post)
            <x-blog.post-summary :$post :$disk />
        @endforeach
    </div>
</div>

