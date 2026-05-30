@props([
    'value' => null,
    'symbol' => 'Rp',
    'empty' => '-',
    'short' => false,
])
@php
    if ($value === null || $value === '' || !is_numeric($value)) {
        $output = $empty;
    } else {
        $num = (float) $value;
        $output = $symbol . number_format($num, 0, ',', '.');
    }
@endphp

<span {{ $attributes->merge(['class' => 'currency-formatted']) }}>
    {{ $output }}
</span>
