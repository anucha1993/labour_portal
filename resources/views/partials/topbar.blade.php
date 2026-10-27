@php $alerts = $p['alerts'] ?? []; @endphp
<header class="topbar">
    <a href="{{ route('home') }}" class="brand">
        <img src="{{ asset('logo168.png') }}" alt="">
        <span>
            <b>The Importer 168</b>
            <small>{{ $p['full_name'] ?? '' }}</small>
        </span>
    </a>
    <a href="{{ route('home') }}#alerts" class="bell {{ $alerts ? 'has' : '' }}" title="แจ้งเตือน / Alerts">
        <x-icon name="bell" />
        @if ($alerts)<span class="dot">{{ count($alerts) }}</span>@endif
    </a>
</header>
