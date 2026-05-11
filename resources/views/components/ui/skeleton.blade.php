@props([
    'width' => '100%',
    'height' => '20px',
    'rounded' => '8px',
    'circle' => false,
])

@php
$style = $circle ? '' : "width: {$width}; height: {$height};";
@endphp

<div class="skeleton {{ $circle ? 'skeleton-circle' : '' }}" 
     {{ $style ? "style=\"{$style}\"" : '' }}
     {{ $attributes->class("rounded-[{$rounded}]") }}>
</div>

@once
@push('styles')
<style>
    .skeleton {
        background: linear-gradient(90deg, #e2e8f0 25%, #f1f5f9 50%, #e2e8f0 75%);
        background-size: 200% 100%;
        animation: skeleton-shimmer 1.5s ease-in-out infinite;
        border-radius: 8px;
        display: block;
    }

    @keyframes skeleton-shimmer {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
</style>
@endpush
@endonce
