@extends('layouts.dashboard')
@section('title', 'Messages | Digitalance')

@section('content')
<div class="animate-fadeUp flex-1 px-8 py-7 overflow-y-auto">
    <div class="flex items-end justify-between mb-8 gap-4 flex-wrap">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900 leading-tight">Pesan</h1>
            <p class="text-slate-500 mt-1 text-[0.95rem]">Kotak masuk negosiasi dan percakapan dengan klien.</p>
        </div>
    </div>

    {{-- Tab system removed as requested --}}

    @if($negotiations->isEmpty())
        <div class="text-center py-16 px-5 bg-white border-2 border-dashed border-slate-200 rounded-[20px]">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-3xl mx-auto mb-4">
                <i class="ri-message-3-line"></i>
            </div>
            <h3 class="font-display text-[1.15rem] font-bold text-slate-700 mb-1">Belum Ada Pesan</h3>
            <p class="text-[13px] text-slate-400">Kamu belum memiliki percakapan negosiasi dengan klien manapun.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 pb-6">
            @foreach($negotiations->groupBy('order_id') as $orderId => $thread)
                @php
                    $latestMsg = $thread->sortByDesc('created_at')->first();
                    $isLog = str_contains(strtolower($latestMsg->message), 'status changed') || str_contains(strtolower($latestMsg->message), 'payment');
                    if($isLog) continue;

                    $order = $latestMsg->order;
                    $clientName = $order->client->name ?? 'Klien';
                    $latestFromClient = $thread->where('sender', 'client')->sortByDesc('created_at')->first();
                    $hasFreelancerResponse = $latestFromClient 
                        ? $thread->where('sender', 'freelancer')->where('created_at', '>', $latestFromClient->created_at)->count() > 0 
                        : false;
                    $needsResponse = $latestFromClient && !$hasFreelancerResponse;
                @endphp
                <div class="msg-card bg-white border {{ $needsResponse ? 'border-amber-300 bg-amber-50/30' : 'border-slate-200' }} rounded-[20px] p-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer" 
                     data-order-id="{{ $orderId }}"
                     data-type="{{ $isLog ? 'log' : 'chat' }}"
                     onclick="openChatModal({{ $orderId }}, '{{ addslashes($clientName) }}')">
                    <div class="flex items-center gap-4 mb-4 pb-4 border-b border-slate-100">
                        <div class="relative">
                            <div class="w-[50px] h-[50px] rounded-2xl bg-gradient-to-br from-[#0f766e] to-teal-500 text-white flex items-center justify-center text-xl shadow-md">
                                <i class="ri-user-smile-line"></i>
                            </div>
                            @if($needsResponse)
                                <div class="absolute -top-1 -right-1 w-5 h-5 rounded-full bg-amber-500 border-2 border-white flex items-center justify-center">
                                    <i class="ri-error-warning-fill text-[8px] text-white"></i>
                                </div>
                            @else
                                <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-white"></div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="font-bold text-slate-900 truncate">{{ $clientName }}</h3>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    @if($needsResponse)
                                        <span class="px-2 py-0.5 rounded-md text-[9px] font-extrabold uppercase bg-amber-100 text-amber-700 animate-pulse">
                                            Perlu Respons
                                        </span>
                                    @endif
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase bg-teal-50 text-teal-700">
                                        Chat
                                    </span>
                                </div>
                            </div>
                            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wider truncate">Order #{{ $orderId }}</p>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <p data-role="preview-time" class="text-[11px] font-bold text-slate-400">{{ $latestMsg->created_at->diffForHumans() }}</p>
                            @if(!$isLog && $needsResponse)
                                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            @endif
                        </div>
                        <p data-role="preview-message" class="text-[13px] text-slate-600 line-clamp-2">
                            @if($latestMsg->sender === 'freelancer')
                                <span class="font-bold text-[#0f766e]">Kamu:</span> 
                            @endif
                            {{ $latestMsg->message }}
                        </p>
                    </div>

                    <div class="mt-3 pt-3 border-t border-slate-100 flex justify-end">
                        <a href="{{ route('freelancer.orders.show', $orderId) }}#riwayat-nego"
                           class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-[10px] font-bold hover:bg-[#0f766e] hover:text-white transition-all flex items-center gap-1">
                            <i class="ri-external-link-line"></i> Buka Nego
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<!-- Modal Chat / Detail -->
<div id="modal-chat" class="fixed inset-0 z-[100] flex items-center justify-center hidden opacity-0 transition-all duration-200">
    <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" onclick="closeChatModal()"></div>
    <div class="bg-white rounded-[24px] w-full max-w-2xl h-[80vh] flex flex-col relative shadow-2xl scale-95 transition-transform duration-200 mx-4 overflow-hidden">
        
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50 flex-shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-[#0f766e]">
                    <i class="ri-user-smile-line text-lg"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-900 text-[15px]" id="chat-client-name">Nama Klien</h3>
                    <p class="text-[12px] text-slate-500 font-bold" id="chat-order-id">Order #...</p>
                </div>
            </div>
            <button onclick="closeChatModal()" class="w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-500 flex items-center justify-center hover:bg-slate-100 transition-colors">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>

        <!-- Body (Pesan) -->
        <div class="flex-1 p-6 overflow-y-auto bg-slate-50/50 flex flex-col gap-4" id="chat-body">
            <!-- Pesan akan dimuat di sini secara statis untuk Phase 3 (menggunakan JS inject dari data PHP) -->
            <div class="text-center text-[12px] font-bold text-slate-400 py-4">Memuat pesan...</div>
        </div>

        <!-- Footer (Kirim) -->
        <div class="p-4 border-t border-slate-100 bg-white flex-shrink-0">
            <form id="chat-send-form-freelancer" action="{{ route('freelancer.negotiations.send-message') }}" method="POST" class="flex items-end gap-3 relative">
                @csrf
                <input type="hidden" name="order_id" id="chat-form-order-id">
                <div class="flex-1">
                    <textarea name="message" rows="1" class="w-full bg-slate-50 border border-slate-200 rounded-[16px] px-5 py-3.5 text-[14px] focus:bg-white focus:border-[#0f766e] focus:ring-4 focus:ring-[#0f766e]/10 outline-none resize-none min-h-[52px] max-h-[120px]" placeholder="Ketik pesan balasan..."></textarea>
                </div>
                <button type="submit" class="no-auto-loader w-[52px] h-[52px] rounded-[16px] bg-[#0f766e] text-white flex items-center justify-center text-xl hover:bg-[#0d6b63] hover:-translate-y-0.5 transition-all shadow-teal-sm flex-shrink-0">
                    <i class="ri-send-plane-fill"></i>
                </button>
            </form>
        </div>
    </div>
</div>

<style>
    #modal-chat.open { display: flex; opacity: 1; }
    #modal-chat.open > div:last-child { transform: scale(1); }
</style>

<script>
    const threadData = @json($negotiations->groupBy('order_id'));
    let activeOrderId = null;
    const mySender = 'freelancer';

    function renderMessageBubble(m) {
        const isMe = m.sender === mySender;
        const time = new Date(m.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
        const align = isMe ? 'items-end ml-auto' : 'items-start mr-auto';
        const bg = isMe ? 'bg-[#0f766e] text-white' : 'bg-white border border-slate-200 text-slate-800';
        const radius = isMe ? 'rounded-[20px] rounded-tr-sm' : 'rounded-[20px] rounded-tl-sm';
        const shadow = isMe ? 'shadow-teal-sm' : 'shadow-sm';

        return `
            <div class="flex flex-col ${align} max-w-[85%]" ${m.id && String(m.id).startsWith('temp-') ? `data-temp-id="${m.id}"` : ''}>
                <div class="px-5 py-3 ${bg} ${radius} ${shadow} text-[14px] leading-relaxed break-words whitespace-pre-wrap">${m.message}</div>
                <span class="text-[10px] font-bold text-slate-400 mt-1.5">${time}</span>
            </div>
        `;
    }

    function appendMessage(orderId, message) {
        const key = String(orderId);

        if (!threadData[key]) {
            threadData[key] = [];
        }
        threadData[key].push(message);

        if (activeOrderId === Number(orderId)) {
            const body = document.getElementById('chat-body');
            body.insertAdjacentHTML('beforeend', renderMessageBubble(message));
            body.scrollTop = body.scrollHeight;
        }

        updateThreadPreview(orderId, message);
    }

    function updateThreadPreview(orderId, message) {
        const card = document.querySelector(`.msg-card[data-order-id="${orderId}"]`);
        if (!card) return;

        const timeEl = card.querySelector('[data-role="preview-time"]');
        const messageEl = card.querySelector('[data-role="preview-message"]');
        const isMe = message.sender === mySender;

        if (timeEl) {
            timeEl.textContent = 'Baru saja';
        }

        if (messageEl) {
            messageEl.textContent = `${isMe ? 'Kamu: ' : ''}${message.message}`;
        }
    }

    function subscribeOrderChannel(orderId) {
        if (!window.Echo) return;

        window.Echo.private('negotiation.' + orderId)
            .listen('NegotiationSent', (e) => {
                appendMessage(orderId, e);
            });
    }

    function openChatModal(orderId, clientName) {
        const key = String(orderId);
        document.getElementById('chat-client-name').innerText = clientName;
        document.getElementById('chat-order-id').innerText = 'Order #' + orderId;
        document.getElementById('chat-form-order-id').value = orderId;
        activeOrderId = Number(orderId);

        const body = document.getElementById('chat-body');
        body.innerHTML = ''; // bersihkan pesan sebelumnya

        if(threadData[key]) {
            const msgs = [...threadData[key]].sort((a,b) => new Date(a.created_at) - new Date(b.created_at));
            msgs.forEach(m => body.insertAdjacentHTML('beforeend', renderMessageBubble(m)));
        }

        const modal = document.getElementById('modal-chat');
        modal.classList.add('open');
        
        // Auto scroll ke bawah
        setTimeout(() => {
            body.scrollTop = body.scrollHeight;
        }, 50);
    }

    function closeChatModal() {
        const modal = document.getElementById('modal-chat');
        activeOrderId = null;
        modal.classList.remove('open', 'opacity-100');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    async function handleSendMessage(event) {
        event.preventDefault();

        const form = event.currentTarget;
        const textarea = form.querySelector('textarea[name="message"]');
        const submitButton = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);

        const msg = textarea.value.trim();
        if (!msg || submitButton.disabled) return;

        // Optimistic UI
        const tempId = 'temp-' + Date.now();
        const tempMsg = {
            id: tempId,
            sender: mySender,
            message: msg,
            created_at: new Date().toISOString(),
            order_id: Number(document.getElementById('chat-form-order-id').value),
        };

        appendMessage(tempMsg.order_id, tempMsg);
        textarea.value = '';
        const originalHeight = textarea.style.height;
        textarea.style.height = 'auto';

        submitButton.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData,
            });

            const payload = await response.json();

            if (!response.ok || !payload.success) {
                throw new Error(payload.message || 'Gagal mengirim pesan.');
            }
        } catch (error) {
            // Rollback optimistic UI
            const body = document.getElementById('chat-body');
            const tempEl = body.querySelector(`[data-temp-id="${tempId}"]`);
            if (tempEl) tempEl.remove();

            // Restore textarea
            textarea.value = msg;
            textarea.style.height = originalHeight;

            if (window.showToast) {
                window.showToast(error.message || 'Pesan gagal terkirim. Coba lagi.', 'danger');
            }
        } finally {
            submitButton.disabled = false;
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        Object.keys(threadData).forEach(orderId => subscribeOrderChannel(orderId));

        const sendForm = document.getElementById('chat-send-form-freelancer');
        if (sendForm) {
            sendForm.addEventListener('submit', handleSendMessage);
        }
    });
</script>
@endsection
