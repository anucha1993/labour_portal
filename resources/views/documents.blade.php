@extends('layouts.app')
@section('title', 'เอกสาร / Documents')
@section('topbar')@include('partials.topbar')@endsection
@section('nav')@include('partials.nav')@endsection

@section('content')
    @php $docs = $p['documents'] ?? []; @endphp
    <section class="card">
        <div class="card-head">
            <span class="card-icon tone-navy"><x-icon name="download-doc" /></span>
            <div><b>My Documents</b><small>เอกสารของฉัน · {{ count($docs) }} ไฟล์</small></div>
        </div>

        @if (! $docs)
            <div class="empty">
                <x-icon name="docs" />
                <p>ยังไม่มีเอกสารในระบบ<br><small>No documents yet</small></p>
            </div>
        @else
            <ul class="doc-list">
                @foreach ($docs as $i => $d)
                    <li>
                        <span class="doc-no">{{ $i + 1 }}</span>
                        <span class="doc-icon {{ $d['is_image'] ? 'is-img' : 'is-pdf' }}"><x-icon :name="$d['is_image'] ? 'file-image' : 'file-pdf'" /></span>
                        <span class="doc-meta">
                            <b>{{ $d['name'] ?: 'Document' }}</b>
                            <small>{{ $d['file_name'] }}@if ($d['size_text'] !== '-') · {{ $d['size_text'] }}@endif</small>
                        </span>
                        <a href="{{ route('documents.open', $d['key']) }}" class="btn btn-sm btn-accent" target="_blank" rel="noopener">
                            <x-icon name="download" /> เปิด / Open
                        </a>
                    </li>
                @endforeach
            </ul>
            <p class="muted small mt">ลิงก์เปิดไฟล์มีอายุ 15 นาที หากหมดอายุให้กดเปิดใหม่<br>Links expire after 15 minutes; tap Open again if needed.</p>
        @endif
    </section>
@endsection
