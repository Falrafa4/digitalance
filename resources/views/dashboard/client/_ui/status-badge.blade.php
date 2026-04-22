@php
  $statusRaw = $status ?? '-';
  $status = strtolower((string) $statusRaw);

  $map = [
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
    'draft' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'icon' => 'ri-draft-line'],
  ];

  $style = $map[$status] ?? ['bg' => 'bg-slate-100', 'text' => 'text-slate-700', 'border' => 'border-slate-200', 'icon' => 'ri-information-line'];
@endphp

<span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[12px] font-extrabold border {{ $style['bg'] }} {{ $style['text'] }} {{ $style['border'] }}">
  <i class="{{ $style['icon'] }}"></i>
  {{ \Illuminate\Support\Str::headline($statusRaw) }}
</span>