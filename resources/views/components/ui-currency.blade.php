@props([
    'value' => null,
    'symbol' => 'Rp',
    'empty' => '-',
    'short' => false, // true: 1.5jt, 500rb
])

@php
    if ($value === null || $value === '' || !is_numeric($value)) {
        $output = $empty;
    } else {
        $num = (float) $value;

        if ($short) {
            if ($num >= 1000000) {
                $output = $symbol . ' ' . number_format($num / 1000000, 1, ',', '.') . 'jt';
            } elseif ($num >= 1000) {
                $output = $symbol . ' ' . number_format($num / 1000, 0, ',', '.') . 'rb';
            } else {
                $output = $symbol . ' ' . number_format($num, 0, ',', '.');
            }
        } else {
            $output = $symbol . ' ' . number_format($num, 0, ',', '.');
        }
    }
@endphp

<span {{ $attributes->merge(['class' => 'currency-formatted']) }}>
    {{ $output }}
</span>
