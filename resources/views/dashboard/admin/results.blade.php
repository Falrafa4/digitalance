@extends('layouts.dashboard')
@section('title', 'Results Management | Digitalance')
@section('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/admin/results.css') }}">
@endsection

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-8 animate-fadeUp">
        <div>
            <h1 class="font-display text-[2.1rem] font-extrabold text-slate-900">Project Results</h1>
            <p class="text-slate-500 text-[0.95rem] mt-1">Pantau hasil pekerjaan yang telah dikirimkan oleh freelancer.</p>
        </div>
        <div class="flex items-center gap-3">
             <div class="bg-white px-5 py-3 rounded-2xl border border-slate-100 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg shadow-sm">
                    <i class="ri-folder-check-line"></i>
                </div>
                <div>
                    <div class="text-[1.2rem] font-black text-slate-900 leading-none">{{ $results->count() }}</div>
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Total Results</div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-[24px] border border-slate-200 overflow-hidden animate-fadeUp-2">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Result Info</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Order ID</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Date</th>
                        <th class="px-6 py-4 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($results as $result)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-[13px] font-black text-slate-900">#RES-{{ $result->id }}</span>
                                    <span class="text-[11px] text-slate-400 font-bold uppercase mt-0.5 truncate max-w-[200px]">{{ $result->version ?? 'Final Work' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[12px] font-bold text-indigo-600">#ORD-{{ $result->order_id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase tracking-wider
                                    @if($result->order->status == 'Completed') bg-emerald-100 text-emerald-700
                                    @elseif($result->order->status == 'In Progress') bg-blue-100 text-blue-700
                                    @else bg-slate-100 text-slate-600 @endif">
                                    {{ $result->order->status ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-[12px] text-slate-500 font-medium">{{ $result->created_at->format('d M Y') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.results.show', $result->id) }}" class="px-4 py-1.5 rounded-xl bg-[#0f766e] text-white text-[11px] font-bold hover:bg-[#0a5e58] transition-all shadow-md shadow-teal-sm">
                                        View Details
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-20 text-center">
                                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-300 text-2xl">
                                    <i class="ri-folder-line"></i>
                                </div>
                                <h3 class="text-slate-900 font-bold">No Results Found</h3>
                                <p class="text-slate-400 text-sm">Belum ada hasil pekerjaan yang diupload.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            @if($results instanceof \Illuminate\Pagination\LengthAwarePaginator && $results->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $results->links() }}
            </div>
            @endif
        </div>
    </div>
@endsection