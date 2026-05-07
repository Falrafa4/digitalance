@props([])
<div id="global-flash" class="fixed top-6 right-6 z-50 space-y-3 pointer-events-none">
    @if(session('success'))
        <div data-flash
            class="max-w-md px-4 py-3 rounded-xl bg-teal-50 border border-teal-200 text-teal-800 shadow-md flex items-start gap-3 pointer-events-auto transform transition-all duration-300"
            role="status">
            <div class="flex-1 text-sm font-semibold">{{ session('success') }}</div>
            <button type="button" aria-label="Tutup" onclick="this.closest('[data-flash]').remove()"
                class="text-teal-700 opacity-80 hover:opacity-100">✕</button>
        </div>
    @endif

    @if(session('error'))
        <div data-flash
            class="max-w-md px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 shadow-md flex items-start gap-3 pointer-events-auto transform transition-all duration-300"
            role="alert">
            <div class="flex-1 text-sm font-semibold">{{ session('error') }}</div>
            <button type="button" aria-label="Tutup" onclick="this.closest('[data-flash]').remove()"
                class="text-red-700 opacity-80 hover:opacity-100">✕</button>
        </div>
    @endif

    @if(session('warning'))
        <div data-flash
            class="max-w-md px-4 py-3 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 shadow-md flex items-start gap-3 pointer-events-auto transform transition-all duration-300"
            role="status">
            <div class="flex-1 text-sm font-semibold">{{ session('warning') }}</div>
            <button type="button" aria-label="Tutup" onclick="this.closest('[data-flash]').remove()"
                class="text-amber-700 opacity-80 hover:opacity-100">✕</button>
        </div>
    @endif

    @if($errors->any())
        <div data-flash
            class="max-w-md px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-800 shadow-md flex items-start gap-3 pointer-events-auto transform transition-all duration-300"
            role="alert">
            <div class="flex-1 text-sm font-semibold">{{ $errors->first() }}</div>
            <button type="button" aria-label="Tutup" onclick="this.closest('[data-flash]').remove()"
                class="text-red-700 opacity-80 hover:opacity-100">✕</button>
        </div>
    @endif

</div>

<script>
    (function () {
        try {
            const wrapper = document.getElementById('global-flash');
            if (!wrapper) return;
            // auto dismiss each flash after 3s with fade
            const flashes = wrapper.querySelectorAll('[data-flash]');
            flashes.forEach((f, i) => {
                setTimeout(() => {
                    f.style.transition = 'opacity 300ms ease, transform 300ms ease';
                    f.style.opacity = '0';
                    f.style.transform = 'translateY(-6px)';
                    setTimeout(() => f.remove(), 320);
                }, 3000 + i * 200);
            });
        } catch (e) { console.error(e) }
    })();
</script>