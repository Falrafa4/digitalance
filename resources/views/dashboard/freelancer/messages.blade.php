@extends('layouts.dashboard')
@section('title', 'Messages | Digitalance')

@section('content')
<div class="animate-fadeUp flex-1 px-8 py-7 overflow-y-auto">
    <div class="flex items-end justify-between mb-8 gap-4 flex-wrap">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900 leading-tight">Messages</h1>
            <p class="text-slate-500 mt-1 text-[0.95rem]">Kotak masuk negosiasi dan percakapan dengan klien.</p>
        </div>
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
                    $order = $latestMsg->order;
                    $clientName = $order->client->user->name ?? $order->client->name ?? 'Klien';
                    $isLog = str_contains(strtolower($latestMsg->message), 'status changed') || str_contains(strtolower($latestMsg->message), 'payment');
                @endphp
                <div class="msg-card bg-white border border-slate-200 rounded-[20px] p-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer" 
                     data-type="{{ $isLog ? 'log' : 'chat' }}"
                     onclick="openChatModal({{ $orderId }}, '{{ addslashes($clientName) }}')">
                    <div class="flex items-center gap-4 mb-4 pb-4 border-b border-slate-100">
                        <div class="relative">
                            <div class="w-[50px] h-[50px] rounded-2xl bg-gradient-to-br from-[#0f766e] to-teal-500 text-white flex items-center justify-center text-xl shadow-md">
                                <i class="ri-user-smile-line"></i>
                            </div>
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-emerald-500 border-2 border-white"></div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="font-bold text-slate-900 truncate">{{ $clientName }}</h3>
                                <span class="px-2 py-0.5 rounded-md text-[10px] font-extrabold uppercase {{ $isLog ? 'bg-blue-50 text-blue-600' : 'bg-teal-50 text-teal-700' }}">
                                    {{ $isLog ? 'Log' : 'Chat' }}
                                </span>
                            </div>
                            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wider truncate">Order #{{ $orderId }}</p>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <p class="text-[11px] font-bold text-slate-400">{{ $latestMsg->created_at->diffForHumans() }}</p>
                            @if(!$isLog && $latestMsg->sender === 'client')
                                <span class="w-2 h-2 rounded-full bg-[#0f766e] animate-pulse"></span>
                            @endif
                        </div>
                        <p class="text-[13px] text-slate-600 line-clamp-2">
                            @if($latestMsg->sender === 'freelancer')
                                <span class="font-bold text-[#0f766e]">Kamu:</span> 
                            @endif
                            {{ $latestMsg->message }}
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
            <form action="{{ route('freelancer.negotiations.send-message') }}" method="POST" class="flex items-end gap-3 relative">
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
    const threadData = @json($negotiations->groupBy('order_id'));

    function openChatModal(orderId, clientName) {
        document.getElementById('chat-client-name').innerText = clientName;
        document.getElementById('chat-order-id').innerText = 'Order #' + orderId;
        document.getElementById('chat-form-order-id').value = orderId;

        const body = document.getElementById('chat-body');
        body.innerHTML = ''; // bersihkan pesan sebelumnya

        if(threadData[orderId]) {
            const msgs = [...threadData[orderId]].sort((a,b) => new Date(a.created_at) - new Date(b.created_at));
            msgs.forEach(m => {
                const isMe = m.sender === 'freelancer';
                const time = new Date(m.created_at).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'});
                
                const align = isMe ? 'self-end' : 'self-start';
                const bg = isMe ? 'bg-[#0f766e] text-white' : 'bg-white border border-slate-200 text-slate-800';
                const radius = isMe ? 'rounded-[18px] rounded-tr-sm' : 'rounded-[18px] rounded-tl-sm';
                const shadow = isMe ? 'shadow-teal-sm' : 'shadow-sm';

                const html = `
                    <div class="flex flex-col ${align} max-w-[80%]">
                        <div class="px-5 py-3 ${bg} ${radius} ${shadow} text-[14px] leading-relaxed break-words whitespace-pre-wrap">${m.message}</div>
                        <span class="text-[10px] font-bold text-slate-400 mt-1 ${isMe ? 'text-right' : 'text-left'}">${time}</span>
                    </div>
                `;
                body.insertAdjacentHTML('beforeend', html);
            });
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
        modal.classList.remove('open', 'opacity-100');
        setTimeout(() => {
            modal.classList.add('hidden');
        }, 200);
    }

    function initMessageTabs() {
        const tabs = document.querySelectorAll('.msg-tab');
        const cards = document.querySelectorAll('.msg-card');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const filter = tab.dataset.filter;

                // Update tab UI
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

                // Filter cards
                cards.forEach(card => {
                    if (filter === 'chat') {
                        card.style.display = card.dataset.type === 'chat' ? 'block' : 'none';
                    } else {
                        card.style.display = card.dataset.type === 'log' ? 'block' : 'none';
                    }
                });
            });
        });

        // Trigger first tab
        const firstTab = document.querySelector('.msg-tab[data-filter="chat"]');
        if(firstTab) firstTab.click();
    }

    document.addEventListener('DOMContentLoaded', () => {
        initMessageTabs();
    });
</script>
@endsection
