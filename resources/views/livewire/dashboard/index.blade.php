<div wire:init="displayMessages">
    <div class="space-y-8">
        <x-page.header
            :title="trans('dashboard.title')"
            icon="layout-dashboard"
            :description="trans('dashboard.description')"
        />
        @include($this->generations->isNotEmpty()
            ? 'livewire.dashboard.letters'
            : 'livewire.dashboard.empty')
    </div>
    @if($this->generations)
        <livewire:dashboard.delete />
        <livewire:dashboard.letter />
        <livewire:dashboard.settings />
    @endif
</div>
