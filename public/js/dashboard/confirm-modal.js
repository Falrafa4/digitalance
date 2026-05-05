(function() {
    window.customConfirm = function(message) {
        return new Promise((resolve) => {
            const overlay = document.createElement('div');
            overlay.className = 'overlay fixed inset-0 z-[9999] bg-slate-900/40 backdrop-blur-sm flex items-center justify-center opacity-0 transition-opacity duration-200';
            
            const box = document.createElement('div');
            box.className = 'modal-box bg-white rounded-[18px] w-full max-w-[400px] shadow-2xl overflow-hidden p-6 text-center transform scale-95 transition-transform duration-200';
            
            box.innerHTML = `
                <div class="w-16 h-16 mx-auto mb-4 bg-orange-50 rounded-full flex items-center justify-center text-[2rem] text-orange-500">
                    <i class="ri-error-warning-fill"></i>
                </div>
                <h3 class="font-display text-[1.15rem] font-extrabold text-slate-900 mb-2">Konfirmasi</h3>
                <p class="text-slate-500 text-[13px] mb-6">${message}</p>
                <div class="flex gap-3">
                    <button type="button" id="btn-confirm-cancel" class="flex-1 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-bold text-[13px] transition hover:bg-slate-200">Batal</button>
                    <button type="button" id="btn-confirm-ok" class="flex-1 py-2.5 rounded-xl bg-teal-600 text-white font-bold text-[13px] transition hover:bg-teal-700 shadow-teal-sm">Yakin</button>
                </div>
            `;
            
            overlay.appendChild(box);
            document.body.appendChild(overlay);
            
            // Trigger animation
            requestAnimationFrame(() => {
                overlay.classList.remove('opacity-0');
                box.classList.remove('scale-95');
                box.classList.add('scale-100');
            });
            
            const close = (result) => {
                overlay.classList.add('opacity-0');
                box.classList.remove('scale-100');
                box.classList.add('scale-95');
                setTimeout(() => {
                    overlay.remove();
                    resolve(result);
                }, 200);
            };
            
            box.querySelector('#btn-confirm-cancel').addEventListener('click', () => close(false));
            box.querySelector('#btn-confirm-ok').addEventListener('click', () => close(true));
        });
    };
})();
