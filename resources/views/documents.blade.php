@extends('layouts.app')
@section('title', __('Documents'))
@section('topbar')@include('partials.topbar')@endsection
@section('nav')@include('partials.nav')@endsection

@section('content')
    @php $docs = $p['documents'] ?? []; @endphp
    <section class="card">
        <div class="card-head">
            <span class="card-icon tone-navy"><x-icon name="download-doc" /></span>
            <div><b>{{ __('My Documents') }}</b><small>{{ t_en('My Documents') ? 'My Documents · ' : '' }}{{ count($docs) }} {{ __('files') }}</small></div>
        </div>

        @if (! $docs)
            <div class="empty">
                <x-icon name="docs" />
                <p>{{ __('No documents yet') }}@if (t_en('No documents yet'))<br><small>No documents yet</small>@endif</p>
            </div>
        @else
            <ul class="doc-list">
                @foreach ($docs as $i => $d)
                    <li>
                        <span class="doc-no">{{ $i + 1 }}</span>
                        <span class="doc-icon {{ $d['is_image'] ? 'is-img' : 'is-pdf' }}"><x-icon :name="$d['is_image'] ? 'file-image' : 'file-pdf'" /></span>
                        <span class="doc-meta">
                            <b>{{ __($d['name'] ?: 'Document') }}</b>
                            <small>{{ $d['file_name'] }}@if ($d['size_text'] !== '-') · {{ $d['size_text'] }}@endif</small>
                        </span>
                        <a href="{{ route('documents.open', $d['key']) }}" class="btn btn-sm btn-accent" target="_blank" rel="noopener">
                            <x-icon name="download" /> {{ __('Open') }}
                        </a>
                    </li>
                @endforeach
            </ul>
            <p class="muted small mt">{{ __('Links expire after 15 minutes; tap Open again if needed.') }}</p>
        @endif
    </section>
@endsection
