<div {{ $attributes->merge(['class' => 'animate-pulse']) }}>
    {{ $slot }}
</div>

@once
@push('styles')
<style>
    .skeleton-block {
        background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
        background-size: 200% 100%;
        animation: skeleton-shimmer 1.5s ease-in-out infinite;
    }

    @keyframes skeleton-shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
@endpush
@endonce
