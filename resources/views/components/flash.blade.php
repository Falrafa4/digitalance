@props([
    'type' => null, // success, warning, error, validation, info
    'message' => null,
])

@php
    /**
     * Komponen flash message (toast) untuk session flash.
     *
     * Peran komponen ini TERPISAH dari notifikasi DB:
     *   - flash.blade.php    = pesan sekali-pakai dari session Laravel (sukses/gagal CRUD)
     *   - notification-drawer = persistent notification per-user dari tabel `notifications`
     *
     * Keduanya tidak menggantikan satu sama lain. Controller existing yang
     * memakai `redirect()->back()->with('success', ...)` tetap bekerja.
     *
     * JS animasi tetap di-handle oleh `public/js/dashboard/shared/flash.js`.
     */
    $typeClasses = [
        'success' => [
            'bg' => 'bg-emerald-50',
            'border' => 'border-emerald-200',
            'shadow' => 'shadow-emerald-500/20',
            'iconBg' => 'bg-emerald-500/20',
            'iconText' => 'text-emerald-600',
            'text' => 'text-emerald-800',
            'closeText' => 'text-emerald-400',
            'closeHoverBg' => 'hover:bg-emerald-100',
            'closeHoverText' => 'hover:text-emerald-800',
            'icon' => 'ri-check-line',
        ],
        'warning' => [
            'bg' => 'bg-amber-50',
            'border' => 'border-amber-200',
            'shadow' => 'shadow-amber-500/20',
            'iconBg' => 'bg-amber-500/20',
            'iconText' => 'text-amber-600',
            'text' => 'text-amber-800',
            'closeText' => 'text-amber-400',
            'closeHoverBg' => 'hover:bg-amber-100',
            'closeHoverText' => 'hover:text-amber-800',
            'icon' => 'ri-alert-line',
        ],
        'error' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'shadow' => 'shadow-red-500/20',
            'iconBg' => 'bg-red-500/20',
            'iconText' => 'text-red-600',
            'text' => 'text-red-800',
            'closeText' => 'text-red-400',
            'closeHoverBg' => 'hover:bg-red-100',
            'closeHoverText' => 'hover:text-red-800',
            'icon' => 'ri-error-warning-line',
        ],
        'validation' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'shadow' => 'shadow-red-500/20',
            'iconBg' => 'bg-red-500/20',
            'iconText' => 'text-red-600',
            'text' => 'text-red-800',
            'closeText' => 'text-red-400',
            'closeHoverBg' => 'hover:bg-red-100',
            'closeHoverText' => 'hover:text-red-800',
            'icon' => 'ri-error-warning-line',
        ],
        'info' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'shadow' => 'shadow-blue-500/20',
            'iconBg' => 'bg-blue-500/20',
            'iconText' => 'text-blue-600',
            'text' => 'text-blue-800',
            'closeText' => 'text-blue-400',
            'closeHoverBg' => 'hover:bg-blue-100',
            'closeHoverText' => 'hover:text-blue-800',
            'icon' => 'ri-information-line',
        ],
    ];

    // Sumber flash: jika tidak ada props, baca dari session Laravel.
    if (is_null($type) && is_null($message)) {
        $flashes = [];

        if (session('success')) {
            $flashes[] = ['type' => 'success', 'message' => session('success')];
        }
        if (session('warning')) {
            $flashes[] = ['type' => 'warning', 'message' => session('warning')];
        }
        $unifiedError = session('login_error') ?? session('register_error') ?? session('error');
        if ($unifiedError) {
            $flashes[] = ['type' => 'error', 'message' => $unifiedError];
        }
        if (isset($errors) && $errors->any()) {
            // Validasi form: tampilkan semua error.
            $flashes[] = ['type' => 'validation', 'message' => $errors->all()];
        }
    } else {
        $flashes = [['type' => $type, 'message' => $message]];
    }
@endphp

<div id="global-flash" class="fixed top-5 right-5 z-[9999] space-y-3 pointer-events-none" aria-live="polite">
    @foreach($flashes as $flash)
        @php
            $classes = $typeClasses[$flash['type']] ?? $typeClasses['info'];
        @endphp
        <div data-flash="{{ $flash['type'] }}"
             class="group max-w-sm flex items-start gap-4 px-6 py-4 rounded-xl shadow-lg {{ $classes['shadow'] }} border {{ $classes['border'] }} bg-white/90 backdrop-blur-md pointer-events-auto"
             role="{{ $flash['type'] === 'error' || $flash['type'] === 'validation' ? 'alert' : 'status' }}">
            <div class="w-9 h-9 rounded-xl {{ $classes['iconBg'] }} {{ $classes['iconText'] }} flex items-center justify-center flex-shrink-0 shadow-inner">
                <i class="{{ $classes['icon'] }} text-[18px]"></i>
            </div>
            <div class="flex-1 min-w-0">
                @if ($flash['type'] === 'validation' && is_array($flash['message']))
                    <p class="text-[13px] font-semibold {{ $classes['text'] }} leading-snug">Validation errors:</p>
                    <ul class="list-disc list-inside text-[12px] text-red-600 space-y-1 pl-4">
                        @foreach ($flash['message'] as $msg)
                            <li>{{ $msg }}</li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-[13px] font-semibold {{ $classes['text'] }} leading-snug">{{ $flash['message'] }}</p>
                @endif
            </div>
            <button type="button" aria-label="Tutup" onclick="this.closest('[data-flash]').remove()"
                class="w-8 h-8 rounded-lg flex items-center justify-center {{ $classes['closeText'] }} {{ $classes['closeHoverBg'] }} {{ $classes['closeHoverText'] }} transition-all hover:shadow-md">
                <i class="ri-close-line text-[16px]"></i>
            </button>
        </div>
    @endforeach
</div>
