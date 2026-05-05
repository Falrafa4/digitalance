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
                @endphp
                <div class="bg-white border border-slate-200 rounded-[20px] p-5 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer" onclick="openChatModal({{ $orderId }}, '{{ addslashes($clientName) }}')">
                    <div class="flex items-center gap-4 mb-4 pb-4 border-b border-slate-100">
                        <div class="relative">
                            <div class="w-[50px] h-[50px] rounded-2xl bg-gradient-to-br from-[#0f766e] to-teal-500 text-white flex items-center justify-center text-xl shadow-md">
                                <i class="ri-user-smile-line"></i>
                            </div>
                        </div>
                        <div class="min-w-0 flex-1">
                            <h3 class="font-bold text-slate-900 truncate">{{ $clientName }}</h3>
                            <p class="text-[12px] font-bold text-slate-400 uppercase tracking-wider truncate">Order #{{ $orderId }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-[11px] font-bold text-slate-400 mb-1.5">{{ $latestMsg->created_at->diffForHumans() }}</p>
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
            const msgs = threadData[orderId].sort((a,b) => new Date(a.created_at) - new Date(b.created_at));
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
</script>
@endsection
