@props(['name'])
@php
    // Inline stroke icons (24x24 unless noted) so the portal needs no icon font.
    $icons = [
        'user' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>',
        'user-circle' => '<circle cx="12" cy="12" r="10"/><circle cx="12" cy="10" r="3"/><path d="M6.2 18.5a7 7 0 0 1 11.6 0"/>',
        'worker' => '<rect x="2" y="2" width="20" height="20" rx="4"/><circle cx="12" cy="7.5" r="2.5"/><path d="M8.5 20l1.5-6.5h4L15.5 20"/><path d="M12 10l-1.3 2.2L12 15l1.3-2.8z"/>',
        'key' => '<circle cx="7.5" cy="15.5" r="4.5"/><path d="M10.7 12.3 21 2m-3 3 3 3m-6 0 3 3"/>',
        'eye' => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>',
        'eye-off' => '<path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>',
        'alert' => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
        'info' => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>',
        'check-circle' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
        'home' => '<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/>',
        'docs' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>',
        'bell' => '<path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/>',
        'passport' => '<rect x="4" y="2" width="16" height="20" rx="2"/><circle cx="12" cy="10" r="3.5"/><path d="M8.5 10h7M12 6.5v7"/><line x1="8" y1="18" x2="16" y2="18"/>',
        'work' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><circle cx="10" cy="13" r="2"/><path d="M7 19a3 3 0 0 1 6 0"/><line x1="15" y1="13" x2="17" y2="13"/><line x1="15" y1="17" x2="17" y2="17"/>',
        'visa' => '<rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/><line x1="6" y1="15" x2="10" y2="15"/>',
        'alarm' => '<circle cx="12" cy="13" r="8"/><path d="M12 9v4l2 2"/><path d="M5 3 2 6m17-3 3 3"/>',
        'check-box' => '<rect x="3" y="3" width="18" height="18" rx="3"/><polyline points="8 12 11 15 16 9"/>',
        'x-box' => '<rect x="3" y="3" width="18" height="18" rx="3"/><line x1="9" y1="9" x2="15" y2="15"/><line x1="15" y1="9" x2="9" y2="15"/>',
        'download-doc' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M12 11v7"/><polyline points="9 15 12 18 15 15"/>',
        'download' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>',
        'file-pdf' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><path d="M8 17v-5h1.5a1.5 1.5 0 0 1 0 3H8m5 2v-5h1a2.5 2.5 0 0 1 0 5h-1"/>',
        'file-image' => '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/>',
        'logout' => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/>',
        'globe' => '<circle cx="12" cy="12" r="10"/><path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>',
        'refresh' => '<polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>',
    ];
    $art = [
        'passport-art' => ['0 0 120 160', '<rect x="6" y="6" width="108" height="148" rx="10"/><circle cx="60" cy="74" r="26"/><ellipse cx="60" cy="74" rx="11" ry="26"/><line x1="34" y1="74" x2="86" y2="74"/><line x1="38" y1="60" x2="82" y2="60"/><line x1="38" y1="88" x2="82" y2="88"/><text x="60" y="34" text-anchor="middle" font-size="13" font-weight="700" letter-spacing="2" fill="currentColor" stroke="none">PASSPORT</text><line x1="40" y1="124" x2="80" y2="124"/><line x1="48" y1="134" x2="72" y2="134"/>'],
        'plane-art' => ['0 0 24 24', '<path d="M17.8 19.2 16 11l3.5-3.5C21 6 21.5 4 21 3c-1-.5-3 0-4.5 1.5L13 8 4.8 6.2c-.5-.1-.9.1-1.1.5l-.3.5c-.2.5-.1 1 .3 1.3L9 12l-2 3H4l-1 1 3 2 2 3 1-1v-3l3-2 3.5 5.3c.3.4.8.5 1.3.3l.5-.2c.4-.3.6-.7.5-1.2z"/>'],
        'route-art' => ['0 0 160 160', '<path d="M10 120 C 30 20, 90 160, 150 30" stroke-dasharray="7 8"/>'],
    ];
    $isArt = isset($art[$name]);
    $viewBox = $isArt ? $art[$name][0] : '0 0 24 24';
    $body = $isArt ? $art[$name][1] : ($icons[$name] ?? $icons['info']);
@endphp
<svg {{ $attributes->merge(['class' => 'ico']) }} viewBox="{{ $viewBox }}" fill="none" stroke="currentColor" stroke-width="{{ $isArt ? 1.5 : 2 }}" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">{!! $body !!}</svg>
