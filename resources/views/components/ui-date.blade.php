@props([
    'value' => null,
    'format' => 'd M Y', // d M Y | d M Y H:i | full | short | relative
    'empty' => '-',
])

@php
    if ($value === null || $value === '') {
        $output = $empty;
    } else {
        try {
            $date = $value instanceof \Carbon\Carbon ? $value : \Carbon\Carbon::parse($value);
            $output = match($format) {
                'full' => $date->format('d M Y, H:i:s'),
                'short' => $date->format('d/m/Y'),
                'relative' => $date->diffForHumans(['locale' => 'id']),
                'd M Y H:i' => $date->format('d M Y, H:i'),
                default => $date->format($format),
            };
        } catch (\Exception $e) {
            $output = $empty;
        }
    }
@endphp

<span {{ $attributes->merge(['class' => 'date-formatted']) }}>
    {{ $output }}
</span>
