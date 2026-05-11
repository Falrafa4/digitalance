<div {{ $attributes->merge(['class' => 'card-base card-base-hover']) }}>
    <div class="flex items-start justify-between gap-4">
        <div class="min-w-0 flex-1">
            <div class="skeleton skeleton-title w-3/4 mb-2"></div>
            <div class="skeleton skeleton-text w-1/2"></div>
        </div>
        <div class="skeleton skeleton-circle w-24 h-6"></div>
    </div>

    <div class="mt-4 space-y-2">
        <div class="skeleton skeleton-text w-full"></div>
        <div class="skeleton skeleton-text w-5/6"></div>
        <div class="skeleton skeleton-text w-4/6"></div>
    </div>

    <div class="mt-4 flex gap-2">
        <div class="skeleton skeleton-btn flex-1"></div>
        <div class="skeleton skeleton-btn flex-1"></div>
    </div>
</div>
