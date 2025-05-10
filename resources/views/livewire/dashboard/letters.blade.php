<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6 lg:gap-8">
    @foreach($this->generations as $generation)
        <x-generated :$generation />
    @endforeach
</div>
