<?php

declare(strict_types=1);

namespace App\Livewire\Blog;

use App\Enums\PostStatusEnum;
use App\Enums\StorageDiskEnum;
use App\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

final class Index extends Component
{
    public function render(): View
    {
        return view(view: 'livewire.blog.index', data: $this->viewData())
            ->title(title: trans(key: 'blog.posts.title'))
            ->layoutData(data: [
                'description' => trans(key: 'blog.posts.description'),
            ]);
    }

    /** @return array<string,mixed> */
    private function viewData(): array
    {
        return [
            'posts' => $this->getPosts(),
            'disk'  => StorageDiskEnum::BLOG_IMAGES->disk(),
        ];
    }

    /** @return Collection<int, BlogPost> */
    private function getPosts(): Collection
    {
        return BlogPost::query()->where(
            column  : 'status',
            operator: '=',
            value   : PostStatusEnum::PUBLISHED
        )->orderBy(
            column   : 'published_at',
            direction: 'desc'
        )->get();
    }
}
