{{-- Standardized Form Layout --}}
@props([
    'title' => 'Form',
    'backUrl' => null,
    'backLabel' => 'Back',
])

<div>
    @if ($backUrl)
        <a href="{{ $backUrl }}"
           class="inline-flex items-center gap-2 text-slate-600 hover:text-[#0f766e] font-semibold mb-6 transition-colors">
            <i class="ri-arrow-left-line"></i>
            {{ $backLabel }}
        </a>
    @endif

    <div class="mb-8">
        <h1 class="font-display text-[2.1rem] font-extrabold">{{ $title }}</h1>
    </div>

    <div class="bg-white rounded-xl border border-slate-200 p-8">
        {{ $slot }}
    </div>
</div>
