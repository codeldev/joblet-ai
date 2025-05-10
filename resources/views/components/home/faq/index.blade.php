@props(['question', 'expanded' => false])
<flux:accordion.item :heading="$question" :$expanded>
    <flux:accordion.content>
        <flux:text class="leading-relaxed space-y-6">
            {{ $slot }}
        </flux:text>
    </flux:accordion.content>
</flux:accordion.item>
