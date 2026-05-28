@props([])
<div id="global-flash" class="fixed top-5 right-5 z-[9999] space-y-3 pointer-events-none" aria-live="polite">
    @if(session('success'))
        <div data-flash="success"
            class="group max-w-sm flex items-start gap-3 px-5 py-4 rounded-2xl shadow-xl shadow-emerald-500/10 border border-emerald-200/60 bg-gradient-to-r from-emerald-50 to-white backdrop-blur-sm pointer-events-auto"
            role="status">
            <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <i class="ri-check-line text-[16px]"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[13px] font-bold text-emerald-800 leading-snug">{{ session('success') }}</p>
            </div>
            <button type="button" aria-label="Tutup" onclick="this.closest('[data-flash]').remove()"
                class="w-7 h-7 rounded-lg flex items-center justify-center text-emerald-400 hover:text-emerald-700 hover:bg-emerald-100/50 transition-all flex-shrink-0 opacity-0 group-hover:opacity-100">
                <i class="ri-close-line text-[14px]"></i>
            </button>
        </div>
    @endif

    @if(session('warning'))
        <div data-flash="warning"
            class="group max-w-sm flex items-start gap-3 px-5 py-4 rounded-2xl shadow-xl shadow-amber-500/10 border border-amber-200/60 bg-gradient-to-r from-amber-50 to-white backdrop-blur-sm pointer-events-auto"
            role="status">
            <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <i class="ri-alert-line text-[16px]"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[13px] font-bold text-amber-800 leading-snug">{{ session('warning') }}</p>
            </div>
            <button type="button" aria-label="Tutup" onclick="this.closest('[data-flash]').remove()"
                class="w-7 h-7 rounded-lg flex items-center justify-center text-amber-400 hover:text-amber-700 hover:bg-amber-100/50 transition-all flex-shrink-0 opacity-0 group-hover:opacity-100">
                <i class="ri-close-line text-[14px]"></i>
            </button>
        </div>
    @endif

    @php
        $unifiedError = session('login_error') ?? session('register_error') ?? session('error');
    @endphp
    @if($unifiedError)
        <div data-flash="error"
            class="group max-w-sm flex items-start gap-3 px-5 py-4 rounded-2xl shadow-xl shadow-red-500/10 border border-red-200/60 bg-gradient-to-r from-red-50 to-white backdrop-blur-sm pointer-events-auto"
            role="alert">
            <div class="w-8 h-8 rounded-xl bg-red-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <i class="ri-error-warning-line text-[16px]"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[13px] font-bold text-red-800 leading-snug">{{ $unifiedError }}</p>
            </div>
            <button type="button" aria-label="Tutup" onclick="this.closest('[data-flash]').remove()"
                class="w-7 h-7 rounded-lg flex items-center justify-center text-red-400 hover:text-red-700 hover:bg-red-100/50 transition-all flex-shrink-0 opacity-0 group-hover:opacity-100">
                <i class="ri-close-line text-[14px]"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div data-flash="validation"
            class="group max-w-sm flex items-start gap-3 px-5 py-4 rounded-2xl shadow-xl shadow-red-500/10 border border-red-200/60 bg-gradient-to-r from-red-50 to-white backdrop-blur-sm pointer-events-auto"
            role="alert">
            <div class="w-8 h-8 rounded-xl bg-red-500 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                <i class="ri-error-warning-line text-[16px]"></i>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[13px] font-bold text-red-800 leading-snug">{{ $errors->first() }}</p>
            </div>
            <button type="button" aria-label="Tutup" onclick="this.closest('[data-flash]').remove()"
                class="w-7 h-7 rounded-lg flex items-center justify-center text-red-400 hover:text-red-700 hover:bg-red-100/50 transition-all flex-shrink-0 opacity-0 group-hover:opacity-100">
                <i class="ri-close-line text-[14px]"></i>
            </button>
        </div>
    @endif
</div>

