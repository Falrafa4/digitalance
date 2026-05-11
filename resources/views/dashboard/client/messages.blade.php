@extends('layouts.dashboard')
@section('title', 'Messages')

@section('content')
<div class="animate-fadeUp flex-1 px-8 py-7 overflow-y-auto">
    <div class="flex items-end justify-between mb-8 gap-4 flex-wrap">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900 leading-tight">Messages</h1>
            <p class="text-slate-500 mt-1 text-[0.95rem]">Kotak masuk negosiasi dan percakapan dengan freelancer.</p>
        </div>
        <a href="{{ route('client.orders.index') }}" class="px-4 py-2.5 rounded-[12px] bg-white border border-slate-200 text-slate-700 font-bold text-[13px] hover:border-[#0f766e] hover:text-[#0f766e] transition-all">
            Ke Orders <i class="ri-arrow-right-line ml-1"></i>
        </a>
    </div>

    <div class="flex items-center gap-6 mb-8 border-b border-slate-100 pb-4">
        <button class="msg-tab active group relative pb-2 transition-all" data-filter="chat">
            <span class="text-[15px] font-extrabold text-[#0f766e]">Chat Percakapan</span>
            <div class="absolute bottom-[-17px] left-0 w-full h-[3px] bg-[#0f766e] rounded-full"></div>
        </button>
        <button class="msg-tab group relative pb-2 transition-all" data-filter="log">
            <span class="text-[15px] font-bold text-slate-400 group-hover:text-slate-600">Log Transaksi</span>
            <div class="absolute bottom-[-17px] left-0 w-0 h-[3px] bg-slate-300 rounded-full group-hover:w-full transition-all"></div>
        </button>
    </div>

    @if(empty($threads) || count($threads) === 0)
        <div class="text-center py-16 px-5 bg-white border-2 border-dashed border-slate-200 rounded-[20px]">
            <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center text-slate-300 text-3xl mx-auto mb-4">
                <i class="ri-message-3-line"></i>
            </div>
            <h3 class="font-display text-[1.15rem] font-bold text-slate-700 mb-1">Belum Ada Pesan</h3>
            <p class="text-[13px] text-slate-400">Kamu belum memiliki percakapan negosiasi dengan freelancer manapun.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 pb-6">
            @foreach($threads as $t)
                @php
                    $order = $t->order;
                    $freelancerName = optional(optional($order->service)->freelancer)->name ?? 'Freelancer';
                    $isLog = str_contains(strtolower($t->message ?? ''), 'status') || str_contains(strtolower($t->message ?? ''), 'payment') || str_contains(strtolower($t->message ?? ''), 'ditolak') || str_contains(strtolower($t->message ?? ''), 'diterima');
                @endphp
                <div class="msg-card bg-white border border-slate-200 rounded-[20px] p-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer" 
                     data-order-id="{{ $t->order_id }}"
                     data-type="{{ $isLog ? 'log' : 'chat' }}"
                     onclick="openChatModal({{ $t->order_id }}, '{{ addslashes($freelancerName) }}')">
                    <div class="flex items-center gap-4 mb-4 pb-4 border-b border-slate-100">
                        <div class="relative">
                            <div class="w-[50px] h-[50px] rounded-2xl bg-gradient-to-br from-[#0f766e] to-teal-500 text-white flex items-center justify-center text-xl shadow-md">
                                <i class="ri-user-smile-line"></i>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-white"></div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="font-bold text-slate-900 truncate">{{ $freelancerName }}</h3>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase {{ $isLog ? 'bg-blue-50 text-blue-600' : 'bg-teal-50 text-teal-700' }}">
                                    {{ $isLog ? 'Log' : 'Chat' }}
                                </span>
                            </div>
                            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wider truncate">Order #{{ $t->order_id }}</p>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <p data-role="preview-time" class="text-[11px] font-bold text-slate-400">{{ $t->created_at->diffForHumans() }}</p>
                            @if(!$isLog && $t->sender === 'freelancer')
                                <span class="w-2 h-2 rounded-full bg-[#0f766e] animate-pulse"></span>
                            @endif
                        </div>
                        <p data-role="preview-message" class="text-[13px] text-slate-600 line-clamp-2">
                            @if($t->sender === 'client')
                                <span class="font-bold text-[#0f766e]">Kamu:</span> 
                            @endif
                            {{ $t->message }}
                        </p>
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
                    <h3 class="font-bold text-slate-900 text-[15px]" id="chat-freelancer-name">Nama Freelancer</h3>
                    <p class="text-[12px] text-slate-500 font-bold" id="chat-order-id">Order #...</p>
                </div>
            </div>
            <button onclick="closeChatModal()" class="w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-500 flex items-center justify-center hover:bg-slate-100 transition-colors">
                <i class="ri-close-line text-lg"></i>
            </button>
        </div>

        <!-- Body (Pesan) -->
        <div class="flex-1 p-6 overflow-y-auto bg-slate-50/50 flex flex-col gap-4" id="chat-body">
            <div class="text-center text-[12px] font-bold text-slate-400 py-4">Memuat pesan...</div>
        </div>

        <!-- Footer (Kirim) -->
        <div class="p-4 border-t border-slate-100 bg-white flex-shrink-0">
            <form id="chat-send-form-client" action="{{ route('client.messages.send') }}" method="POST" class="flex items-end gap-3 relative">
                @csrf
                <input type="hidden" name="order_id" id="chat-form-order-id">
                <div class="flex-1">
                    <textarea name="message" rows="1" class="w-full bg-slate-50 border border-slate-200 rounded-[16px] px-5 py-3.5 text-[14px] focus:bg-white focus:border-[#0f766e] focus:ring-4 focus:ring-[#0f766e]/10 outline-none resize-none min-h-[52px] max-h-[120px]" placeholder="Ketik pesan balasan..."></textarea>
                </div>
                <button type="submit" class="w-[52px] h-[52px] rounded-[16px] bg-[#0f766e] text-white flex items-center justify-center text-xl hover:bg-[#0d6b63] hover:-translate-y-0.5 transition-all shadow-teal-sm flex-shrink-0">
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
    const threadData = {};
    @if(!empty($threads))
        @foreach($threads->groupBy('order_id') as $orderId => $msgs)
            threadData[{{ $orderId }}] = @json($msgs->sortBy('created_at')->values());
        @endforeach
    @endif
    let activeOrderId = null;
    const mySender = 'client';

    function renderMessageBubble(m) {
        const isMe = m.sender === mySender;
        const time = new Date(m.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
        const align = isMe ? 'self-end' : 'self-start';
        const bg = isMe ? 'bg-[#0f766e] text-white' : 'bg-white border border-slate-200 text-slate-800';
        const radius = isMe ? 'rounded-[18px] rounded-tr-sm' : 'rounded-[18px] rounded-tl-sm';
        const shadow = isMe ? 'shadow-teal-sm' : 'shadow-sm';

        return `
            <div class="flex flex-col ${align} max-w-[80%]">
                <div class="px-5 py-3 ${bg} ${radius} ${shadow} text-[14px] leading-relaxed break-words whitespace-pre-wrap">${m.message}</div>
                <span class="text-[10px] font-bold text-slate-400 mt-1 ${isMe ? 'text-right' : 'text-left'}">${time}</span>
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

    function openChatModal(orderId, freelancerName) {
        const key = String(orderId);
        document.getElementById('chat-freelancer-name').innerText = freelancerName;
        document.getElementById('chat-order-id').innerText = 'Order #' + orderId;
        document.getElementById('chat-form-order-id').value = orderId;
        activeOrderId = Number(orderId);

        const body = document.getElementById('chat-body');
        body.innerHTML = '';

        if(threadData[key]) {
            threadData[key].forEach(m => body.insertAdjacentHTML('beforeend', renderMessageBubble(m)));
        }

        const modal = document.getElementById('modal-chat');
        modal.classList.add('open');
        
        setTimeout(() => { body.scrollTop = body.scrollHeight; }, 50);
    }

    function closeChatModal() {
        const modal = document.getElementById('modal-chat');
        activeOrderId = null;
        modal.classList.remove('open', 'opacity-100');
        setTimeout(() => { modal.classList.add('hidden'); }, 200);
    }

    async function handleSendMessage(event) {
        event.preventDefault();

        const form = event.currentTarget;
        const textarea = form.querySelector('textarea[name="message"]');
        const submitButton = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);

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

            appendMessage(Number(payload.data.order_id), payload.data);
            textarea.value = '';
        } catch (error) {
            if (window.showToast) {
                window.showToast(error.message, 'danger');
            }
        } finally {
            submitButton.disabled = false;
        }
    }

    function initMessageTabs() {
        const tabs = document.querySelectorAll('.msg-tab');
        const cards = document.querySelectorAll('.msg-card');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const filter = tab.dataset.filter;
                tabs.forEach(t => {
                    t.classList.remove('active');
                    t.querySelector('span').classList.replace('text-[#0f766e]', 'text-slate-400');
                    t.querySelector('span').classList.remove('font-extrabold');
                    t.querySelector('span').classList.add('font-bold');
                    t.querySelector('div').classList.replace('w-full', 'w-0');
                    t.querySelector('div').classList.replace('bg-[#0f766e]', 'bg-slate-300');
                });
                tab.classList.add('active');
                tab.querySelector('span').classList.replace('text-slate-400', 'text-[#0f766e]');
                tab.querySelector('span').classList.replace('font-bold', 'font-extrabold');
                tab.querySelector('div').classList.replace('w-0', 'w-full');
                tab.querySelector('div').classList.replace('bg-slate-300', 'bg-[#0f766e]');

                cards.forEach(card => {
                    if (filter === 'chat') {
                        card.style.display = card.dataset.type === 'chat' ? 'block' : 'none';
                    } else {
                        card.style.display = card.dataset.type === 'log' ? 'block' : 'none';
                    }
                });
            });
        });

        const firstTab = document.querySelector('.msg-tab[data-filter="chat"]');
        if(firstTab) firstTab.click();
    }

    document.addEventListener('DOMContentLoaded', () => {
        initMessageTabs();

        Object.keys(threadData).forEach(orderId => subscribeOrderChannel(orderId));

        const sendForm = document.getElementById('chat-send-form-client');
        if (sendForm) {
            sendForm.addEventListener('submit', handleSendMessage);
        }
    });
</script>
@endsection
