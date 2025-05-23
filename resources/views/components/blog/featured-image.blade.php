@props(['disk', 'post'])
<picture>
    @php
        $files = collect($post->featured->files)
                ->filter(function($file) {
                    return $file['width'] !== '1920';
                })
                ->sortBy('width')
                ->values();

        $fileCount = $files->count();
    @endphp

    @foreach($files as $index => $file)
        @if($index === 0)
            <source
                media="(max-width: {{ $file['width'] }}px)"
                srcset="{{ $disk->url($file['image']) }}"
            />
        @elseif($index < $fileCount - 1)
            <source
                media="(min-width: {{ (int)$files[$index-1]['width'] + 1 }}px) and (max-width: {{ $file['width'] }}px)"
                srcset="{{ $disk->url($file['image']) }}"
            />
        @else
            <source
                media="(min-width: {{ (int)$files[$index-1]['width'] + 1 }}px)"
                srcset="{{ $disk->url($file['image']) }}"
            />
        @endif
    @endforeach

    <img {{ $attributes->merge([
        'class'   => 'w-full h-full object-cover rounded-md sm:rounded-lg md:rounded-xl',
        'loading' => 'lazy',
        'alt'     => $post->featured->description,
        'title'   => $post->featured->description,
        'src'     => $disk->url($files->firstWhere('width', '700')['image'] ?? $files->first()['image']),
    ]) }} />
</picture>
