<div wire:init="displayMessages" class="min-h-screen bg-zinc-100 dark:bg-zinc-900 bg-texture-a-light dark:bg-texture-a-dark">
    @include('livewire.home.sections.hero')
    <x-home.divider />
    @include('livewire.home.sections.benefits')
    @include('livewire.home.sections.process')
    @include('livewire.home.sections.testimonials')
    @include('livewire.home.sections.pricing')
    @include('livewire.home.sections.faq')
    <x-home.divider />
    @include('livewire.home.sections.action')
</div>
