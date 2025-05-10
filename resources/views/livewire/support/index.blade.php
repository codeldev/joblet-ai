<div class="space-y-6 faq-support">
    <x-page.header
        :title="trans('support.title')"
        icon="life-buoy"
    >
        <flux:text class="space-y-3 leading-relaxed">
            <p>{{ trans('support.description.1', ['app' => $appName]) }}</p>
            <p>{!! trans('support.description.2', ['email' => $contact]) !!}</p>
        </flux:text>
    </x-page.header>
    <div class="space-y-4 xs:space-y-6 md:space-y-8 faq-support">
        @include('livewire.support.general')
        @include('livewire.support.pricing')
        @include('livewire.support.generation')
        @include('livewire.support.technical')
        @include('livewire.support.privacy')
        @include('livewire.support.support')
    </div>
</div>
