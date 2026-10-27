@props(['days' => null, 'level' => 'none', 'size' => 'lg'])
@php
    $tone = match ($level) { 'expired', 'critical' => 'danger', 'warning' => 'warn', 'ok' => 'ok', default => 'muted' };
@endphp
<span {{ $attributes->merge(['class' => "remaining remaining-$size tone-$tone"]) }}>
    @if ($days === null)
        <span class="num">-</span>
    @elseif ($days < 0)
        <span class="num">{{ number_format(abs($days)) }}</span> <span class="unit">วัน / Days</span>
        <span class="sub">หมดอายุแล้ว / Expired</span>
    @elseif ($days === 0)
        <span class="num">0</span> <span class="unit">วัน / Days</span>
        <span class="sub">หมดอายุวันนี้ / Expires today</span>
    @else
        <span class="num">{{ number_format($days) }}</span> <span class="unit">วัน / Days</span>
    @endif
</span>
