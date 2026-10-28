@php
    $locale = app()->getLocale();
    // Google Fonts family per script; Inter is always loaded for numbers / English.
    $fonts = ['my' => 'Noto+Sans+Myanmar:wght@400;500;600;700', 'th' => 'Noto+Sans+Thai:wght@400;500;600;700', 'km' => 'Noto+Sans+Khmer:wght@400;500;600;700', 'lo' => 'Noto+Sans+Lao:wght@400;500;600;700'];
    $fontQuery = 'family=Inter:wght@400;500;600;700;800' . (isset($fonts[$locale]) ? '&family=' . $fonts[$locale] : '');
@endphp
<!DOCTYPE html>
<html lang="{{ $locale }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#cfe3fb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Importer 168">
    <title>@hasSection('title')@yield('title') · @endif{{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('logo168.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192.png') }}">
    <link rel="manifest" href="{{ asset('manifest.webmanifest') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?{{ $fontQuery }}&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/portal.css') }}?v={{ filemtime(public_path('css/portal.css')) }}">
</head>
<body class="@yield('body-class')">
<div class="bg-art" aria-hidden="true">
    <x-icon name="passport-art" class="art art-passport" />
    <x-icon name="plane-art" class="art art-plane" />
    <x-icon name="plane-art" class="art art-plane-2" />
    <x-icon name="route-art" class="art art-route" />
</div>

<div class="phone">
    @hasSection('topbar')
        @yield('topbar')
    @endif

    <main class="screen @yield('screen-class')">
        @include('partials.flash')
        @yield('content')
    </main>

    @hasSection('nav')
        @yield('nav')
    @endif
</div>

<script src="{{ asset('js/portal.js') }}?v={{ filemtime(public_path('js/portal.js')) }}"></script>
@stack('scripts')
</body>
</html>
