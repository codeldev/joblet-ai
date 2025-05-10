@props(['generation'])
<flux:card
    class="relative p-4 md:p-5 lg:p-6"
    wire:key="{{ $generation->id }}"
    x-data="{
        showOptions  : false,
        showSettings : false,
        displayOptions()
        {
            this.showOptions  = true;
            this.showSettings = false;
        },
        displaySettings()
        {
            this.showOptions  = false;
            this.showSettings = true;
        },
        closeAll()
        {
            this.showOptions  = false;
            this.showSettings = false;
        }
    }"
>
    <div class="space-y-3 sm:space-y-4">
        <x-generated.header :$generation />
        <x-generated.dates :$generation />
    </div>
    <x-generated.options :$generation />
    <x-generated.settings :$generation />
</flux:card>
