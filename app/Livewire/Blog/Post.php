<?php

declare(strict_types=1);

namespace App\Livewire\Blog;

use App\Enums\StorageDiskEnum;
use App\Models\BlogPost;
use Illuminate\Contracts\View\View;
use Livewire\Component;

final class Post extends Component
{
    public BlogPost $post;

    public function render(): View
    {
        return view(view: 'livewire.blog.post', data: $this->viewData())
            ->title(title: $this->post->title)
            ->layoutData(data: [
                'description' => $this->post->description,
            ]);
    }

    /** @return array<string,mixed> */
    private function viewData(): array
    {
        return [
            'disk' => StorageDiskEnum::BLOG_IMAGES->disk(),
        ];
    }
}
