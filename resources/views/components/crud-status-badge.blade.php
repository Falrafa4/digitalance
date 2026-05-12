{{-- Standardized CRUD Status Badge (All Modules) --}}
@props([
    'status' => '',
])

@php
    // Unified status color mapping across ALL modules
    $statusColors = [
        // Service Status
        'Draft' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'icon' => 'ri-draft-line'],
        'Pending' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'ri-time-line'],
        'Approved' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'ri-check-line'],
        'Rejected' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'ri-close-line'],

        // Order Status
        'Paid' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'ri-check-double-line'],
        'In Progress' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'ri-loader-4-line'],
        'Revision' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'icon' => 'ri-edit-line'],
        'Completed' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'ri-checkbox-circle-line'],
        'Cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-700', 'icon' => 'ri-close-circle-line'],

        // Offer/Negotiation Status
        'Sent' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'icon' => 'ri-send-plane-line'],
        'Accepted' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'icon' => 'ri-check-line'],
        'Negotiated' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-700', 'icon' => 'ri-chat-3-line'],
    ];

    $statusLabel = ucfirst(str_replace('_', ' ', $status ?: 'Unknown'));
    $style = $statusColors[trim($statusLabel)] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'icon' => 'ri-question-line'];
@endphp

<span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-sm font-semibold {{ $style['bg'] }} {{ $style['text'] }}">
    <i class="text-xs {{ $style['icon'] }}"></i>
    {{ $statusLabel }}
</span>
