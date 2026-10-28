@props(['days' => null, 'level' => 'none', 'size' => 'lg'])
@php
    $tone = match ($level) { 'expired', 'critical' => 'danger', 'warning' => 'warn', 'ok' => 'ok', default => 'muted' };
    $unit = __('days') . (t_en('days') ? ' / Days' : '');
@endphp
<span {{ $attributes->merge(['class' => "remaining remaining-$size tone-$tone"]) }}>
    @if ($days === null)
        <span class="num">-</span>
    @elseif ($days < 0)
        <span class="num">{{ number_format(abs($days)) }}</span> <span class="unit">{{ $unit }}</span>
        <span class="sub">{{ __('Expired') }}</span>
    @elseif ($days === 0)
        <span class="num">0</span> <span class="unit">{{ $unit }}</span>
        <span class="sub">{{ __('Expires today') }}</span>
    @else
        <span class="num">{{ number_format($days) }}</span> <span class="unit">{{ $unit }}</span>
    @endif
</span>
