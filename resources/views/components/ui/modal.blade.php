<div class="fixed inset-0 z-50 flex items-center justify-center p-4"
     x-data="{ show: false }"
     x-show="show"
     x-cloak
     @keydown.escape.window="show = false"
     @open-modal.window="show = true"
     @close-modal.window="show = false"
     role="dialog"
     aria-modal="true"
     :aria-labelledby="$refs.title ? 'modal-title' : null"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
         @click="show = false; $dispatch('close-modal')"></div>

    <div class="relative bg-white rounded-[24px] shadow-2xl w-full max-w-{{ $maxWidth ?? 'lg' }} p-6 sm:p-8 transform transition-all duration-200"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100">

        <button type="button"
                @click="show = false; $dispatch('close-modal')"
                class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-500 transition-colors"
                aria-label="Close modal">
            <i class="ri-close-line"></i>
        </button>

        {{ $slot }}
    </div>
</div>
