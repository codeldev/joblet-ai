@props([
    'letter',
    'seconds'   => round(random_int(3000, 7000) / 1000, 3),
    'previewId' => null,
])
<div {{ $attributes->merge(['class' => 'relative flex items-center justify-center lg:justify-end']) }}>
    <div class="relative w-full max-w-lg lg:max-w-md xl:max-w-lg">
        <div class="absolute -inset-0.5 rounded-2xl bg-gradient-to-r from-indigo-400 to-indigo-600 opacity-30 blur-sm md:blur-md lg:blur-lg dark:from-indigo-500/80 dark:to-indigo-300/80"></div>
        <div class="relative overflow-hidden rounded-xl border border-zinc-200 bg-white shadow-xl dark:border-zinc-700 dark:bg-zinc-800">
            <div class="flex items-center space-x-2 border-b border-zinc-200 bg-zinc-100 px-4 py-2 dark:border-zinc-700 dark:bg-zinc-800/80">
                <div class="flex space-x-1.5">
                    <div class="h-3 w-3 rounded-full bg-red-500"></div>
                    <div class="h-3 w-3 rounded-full bg-yellow-500"></div>
                    <div class="h-3 w-3 rounded-full bg-green-500"></div>
                </div>
                <div class="ml-2 flex-1">
                    <p class="text-xs font-medium text-right text-zinc-500 dark:text-zinc-400">
                        {{ $letter['document'] }}
                    </p>
                </div>
            </div>
            <div class="p-4 xs:p-6 space-y-4 min-h-[307px]">
                <flux:text class="space-y-3 text-xs xs:text-sm">
                    @if($previewId)
                        <div id="{{ $previewId }}" class="space-y-3"></div>
                    @else
                        @foreach($letter['lines'] as $line)
                            <p>{{ $line }}</p>
                        @endforeach
                        <p class="text-zinc-400 dark:text-zinc-500">
                            {{ trans('home.preview.example.letter.continues') }}
                        </p>
                    @endif
                </flux:text>
                @if($previewId)
                    <div class="justify-end mt-4 hidden" id="{{ $previewId }}-generated">
                        <span class="inline-flex items-center rounded-md bg-indigo-100-50 px-2 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                            {{ trans('home.hero.preview.generated', ['seconds' => $seconds]) }}
                        </span>
                    </div>
                @else
                    <div class="flex justify-end mt-4">
                        <span class="inline-flex items-center rounded-md bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                            {{ trans('home.hero.preview.generated', ['seconds' => $seconds]) }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
