{{-- Standardized CRUD Status Badge (All Modules) --}}
@props([
    'status' => '',
    'border' => false,
])

@php
    $statusColors = [
        'Draft' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'icon' => 'ri-draft-line'],
        'Pending' => ['bg' => $border ? 'bg-amber-50' : 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'ri-time-line', 'border' => 'border-amber-100'],
        'Approved' => ['bg' => $border ? 'bg-emerald-50' : 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'ri-check-line', 'border' => 'border-emerald-100'],
        'Rejected' => ['bg' => $border ? 'bg-rose-50' : 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'ri-close-line', 'border' => 'border-red-100'],
        'Paid' => ['bg' => $border ? 'bg-emerald-50' : 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'ri-check-double-line', 'border' => 'border-emerald-100'],
        'In Progress' => ['bg' => $border ? 'bg-indigo-50' : 'bg-blue-100', 'text' => $border ? 'text-indigo-700' : 'text-blue-700', 'icon' => 'ri-loader-4-line', 'border' => 'border-indigo-100'],
        'Revision' => ['bg' => $border ? 'bg-violet-50' : 'bg-orange-100', 'text' => $border ? 'text-violet-700' : 'text-orange-700', 'icon' => 'ri-edit-line', 'border' => 'border-violet-100'],
        'Completed' => ['bg' => $border ? 'bg-emerald-50' : 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'ri-checkbox-circle-line', 'border' => 'border-emerald-100'],
        'Cancelled' => ['bg' => $border ? 'bg-rose-50' : 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'ri-close-circle-line', 'border' => 'border-red-100'],
        'Sent' => ['bg' => $border ? 'bg-amber-50' : 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'ri-send-plane-line', 'border' => 'border-amber-100'],
        'Negotiated' => ['bg' => $border ? 'bg-cyan-50' : 'bg-blue-100', 'text' => $border ? 'text-cyan-700' : 'text-blue-700', 'icon' => 'ri-chat-3-line', 'border' => 'border-cyan-100'],
    ];

    $statusLabel = ucfirst(str_replace('_', ' ', $status ?: 'Unknown'));
    $style = $statusColors[trim($statusLabel)] ?? ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'icon' => 'ri-information-line', 'border' => 'border-slate-200'];
    $borderClass = $border ? ($style['border'] ?? 'border-slate-200') : '';
@endphp

<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-[12px] font-extrabold border {{ $style['bg'] }} {{ $style['text'] }} {{ $borderClass }}">
    <i class="text-xs {{ $style['icon'] }}"></i>
    {{ $statusLabel }}
</span>
