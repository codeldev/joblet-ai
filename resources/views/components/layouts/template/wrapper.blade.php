<div x-data="{
    loaded: false,
    setTheme(){ $flux.appearance = localStorage.getItem('flux.appearance') ?? 'dark' },
    setLoaded(){ this.loaded = true; },
    init(){ this.setTheme(); this.setLoaded();}
}"
x-on:redirect.window="setTimeout(() => window.location.href = $event.detail.redirect, $event.detail.timeout)"
x-show="loaded"
x-cloak>
    {{ $slot }}
</div>
