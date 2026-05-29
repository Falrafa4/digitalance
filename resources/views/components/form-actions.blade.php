{{-- Standardized Form Action Buttons --}}
@props([
    'submitLabel' => 'Simpan',
    'cancelUrl' => null,
    'deleteUrl' => null,
    'isDangerous' => false,
])

<div class="flex items-center justify-between gap-4 pt-6 border-t border-slate-200">
    <div>
        @if ($deleteUrl)
            <form action="{{ $deleteUrl }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin? Tindakan ini tidak dapat dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-red-100 text-red-700 font-semibold hover:bg-red-200 transition-colors">
                    <i class="ri-delete-bin-line"></i>
                    Hapus
                </button>
            </form>
        @endif
    </div>

    <div class="flex items-center gap-3">
        @if ($cancelUrl)
            <a href="{{ $cancelUrl }}"
               class="px-4 py-2 rounded-lg border border-slate-300 text-slate-700 font-semibold hover:bg-slate-50 transition-colors">
                Batal
            </a>
        @endif

        <button type="submit"
                class="inline-flex items-center gap-2 px-6 py-2 rounded-lg {{ $isDangerous ? 'bg-red-600 hover:bg-red-700' : 'bg-[#0f766e] hover:bg-teal-800' }} text-white font-semibold transition-colors">
            <i class="ri-check-line"></i>
            {{ $submitLabel }}
        </button>
    </div>
</div>
