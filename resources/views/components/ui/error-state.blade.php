{{-- Error State Component --}}
@props([
    'icon' => 'ri-error-warning-line',
    'title' => 'Something went wrong',
    'description' => 'An error occurred while loading this content.',
    'retryUrl' => null,
])

<div class="flex flex-col items-center justify-center py-12 px-4 text-center">
    <div class="mb-4">
        <i class="text-5xl text-red-300 {{ $icon }}"></i>
    </div>

    <h3 class="text-lg font-semibold text-slate-700 mb-2">{{ $title }}</h3>

    <p class="text-slate-500 text-center mb-6 max-w-sm">{{ $description }}</p>

    @if ($retryUrl)
        <a href="{{ $retryUrl }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-black transition-colors">
            <i class="ri-refresh-line"></i>
            Coba Lagi
        </a>
    @else
        <button onclick="location.reload()"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-slate-900 text-white font-semibold hover:bg-black transition-colors">
            <i class="ri-refresh-line"></i>
            Coba Lagi
        </button>
    @endif
</div>
