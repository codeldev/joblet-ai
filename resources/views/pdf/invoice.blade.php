<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <title>
            {{ trans('invoice.title', [
                'name' => $settings['name']
            ]) }}
        </title>
        <meta charset="utf-8">
        @vite(['resources/css/invoice.css'])
    </head>
    <body class="relative font-sans antialiased text-sm  text-neutral-800 leading-none p-12">
        <div class="space-y-12">
            <div class="flex items-start justify-between gap-12">
                <div class="flex-1 space-y-6">
                    <x-invoice.branding :$settings />
                    <x-invoice.customer :$user />
                </div>
                <div class="w-80 flex-none space-y-5">
                    <x-invoice.info :$order :$payment />
                </div>
            </div>
            <div class="space-y-10">
                <x-invoice.items :$order :$payment />
                <x-invoice.paid :$settings />
            </div>
        </div>
        <x-invoice.footer :$settings />
    </body>
</html>
