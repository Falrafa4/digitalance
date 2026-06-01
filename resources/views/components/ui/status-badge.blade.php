{{-- Universal Status Badge for all modules --}}
@props([
    'status' => '',
    'border' => false,
    'showIcon' => true,
])

@php
    $statusKey = strtolower(trim((string) $status));
    $statusLabel = $statusKey === '' || $statusKey === '-' ? 'Tidak Diketahui' : \Illuminate\Support\Str::headline($status);

    if (in_array($statusKey, ['skomda student', 'skomda'], true)) {
        $statusLabel = 'Skomda Students';
    }

    $map = [
        'draft' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'icon' => 'ri-draft-line'],
        'pending' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100', 'icon' => 'ri-time-line'],
        'negotiated' => ['bg' => 'bg-cyan-50', 'text' => 'text-cyan-700', 'border' => 'border-cyan-100', 'icon' => 'ri-chat-3-line'],
        'paid' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'icon' => 'ri-bank-card-line'],
        'in progress' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-100', 'icon' => 'ri-loader-4-line'],
        'in_progress' => ['bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'border' => 'border-indigo-100', 'icon' => 'ri-loader-4-line'],
        'revision' => ['bg' => 'bg-violet-50', 'text' => 'text-violet-700', 'border' => 'border-violet-100', 'icon' => 'ri-edit-2-line'],
        'completed' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'icon' => 'ri-check-line'],
        'cancelled' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-100', 'icon' => 'ri-close-line'],
        'rejected' => ['bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'border' => 'border-rose-100', 'icon' => 'ri-close-circle-line'],
        'approved' => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'border' => 'border-emerald-100', 'icon' => 'ri-badge-check-line'],
        'sent' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'border' => 'border-amber-100', 'icon' => 'ri-send-plane-line'],
        'client' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'border' => 'border-blue-100', 'icon' => 'ri-user-line'],
        'freelancer' => ['bg' => 'bg-teal-50', 'text' => 'text-teal-700', 'border' => 'border-teal-100', 'icon' => 'ri-user-star-line'],
        'skomda student' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-200', 'icon' => 'ri-user-settings-line'],
        'skomda' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'border' => 'border-slate-200', 'icon' => 'ri-user-settings-line'],
    ];

    $defaultStyle = ['bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'border' => 'border-slate-200', 'icon' => 'ri-information-line'];
    $style = $map[$statusKey] ?? $defaultStyle;
    $borderClass = $border ? ($style['border'] ?? 'border-slate-200') : '';
@endphp

<span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[12px] font-bold border {{ $style['bg'] }} {{ $style['text'] }} {{ $borderClass }}">
    @if($showIcon)
        <i class="{{ $style['icon'] }} text-[11px]"></i>
    @endif
    {{ $statusLabel }}
</span>