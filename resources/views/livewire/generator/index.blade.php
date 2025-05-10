<div>
    <div class="space-y-6 lg:space-y-8">
        <x-page.header
            :title="trans('generator.title')"
            icon="file-text"
            :description="trans('generator.description')"
        />
        <div class="space-y-6 lg:space-y-8">
            @auth
                <livewire:account.credits :generate="false" />
            @endauth
            @include('livewire.generator.form')
        </div>
    </div>
    @include('livewire.generator.options')
    @include('livewire.generator.settings')
    @include('livewire.generator.auth')
    <livewire:dashboard.letter />
</div>
