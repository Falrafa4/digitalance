@php
    $photoField = $photoField ?? 'profile_photo';
    $nameField = $nameField ?? 'name';
    $emailField = $emailField ?? 'email';
    
    $photoUrl = null;
    if (isset($user)) {
        if ($user?->avatar) {
            $photoUrl = asset('storage/' . $user->avatar);
        } elseif ($user?->{$photoField}) {
            $photoUrl = asset('storage/' . $user->{$photoField});
        }
    }
    
    $fallbackName = $user?->{$nameField} ?? $user?->{$emailField} ?? 'User';
    
    $bgColor = match($role ?? '') {
        'admin' => '0f766e',
        'client' => '0f766e',
        'freelancer' => '0f766e',
        default => '0f766e'
    };
    
    $size = $size ?? 42;
    $seedParam = $seedParam ?? $fallbackName;
    
    $fallbackUrl = 'https://api.dicebear.com/7.x/initials/svg?seed=' . urlencode($seedParam) . '&background=' . $bgColor . '&color=fff&size=' . $size;
@endphp

<img src="{{ $photoUrl ?? $fallbackUrl }}"
    alt="{{ $fallbackName }}"
    class="{{ $class ?? 'w-10 h-10 rounded-xl object-cover' }}"
    @if(!$photoUrl) onerror="this.onerror=null;this.src='{{ $fallbackUrl }}'" @endif
    loading="lazy" decoding="async" />