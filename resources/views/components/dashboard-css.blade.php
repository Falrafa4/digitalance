{{-- Dashboard Layout Styles Partial --}}
{{-- Include this in <head> after main dashboard.css --}}
@php
    $segment = request()->segment(1);
    $roleCss = match($segment) {
        'admin' => 'css/dashboard/admin/dashboard.css',
        'client' => 'css/dashboard/client/dashboard.css',
        'freelancer' => 'css/dashboard/freelancer/dashboard.css',
        default => 'css/dashboard/admin/dashboard.css',
    };
@endphp
<link rel="stylesheet" href="{{ asset($roleCss) }}">