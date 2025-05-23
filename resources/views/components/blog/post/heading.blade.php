@props(['post'])
<div class="!space-y-4">
    <flux:heading level="1" class="!text-lg sm:!text-xl md:!text-2xl lg:!text-3xl leading-snug !text-center xs:!text-left">
        {{ $post->title}}
    </flux:heading>
    <flux:subheading class="leading-relaxed !text-center xs:!text-left">
        {{ $post->summary}}
    </flux:subheading>
</div>
