@extends('layouts.app')
@section('title', __('Home'))
@section('topbar')@include('partials.topbar')@endsection
@section('nav')@include('partials.nav')@endsection

@section('content')
    @php
        $st = $p['status'];
        $emp = $p['employer'];
        $alerts = $p['alerts'] ?? [];
        $docs = $p['documents'] ?? [];
        $statusText = __($st['label_en'] ?? '') ?: ($st['label_en'] ?? '-');
    @endphp

    {{-- ================================================== Identity (mockup screen 2) --}}
    <section class="card card-identity">
        @if (! empty($p['photo_url']))
            <div class="identity-icon has-photo"><img src="{{ $p['photo_url'] }}" alt="{{ $p['full_name'] }}"></div>
        @else
            <div class="identity-icon" aria-hidden="true"><x-icon name="worker" /></div>
        @endif
        <div class="field">
            <div class="label">{{ __('Name') }} <span class="en">{{ t_en('Name') }}</span></div>
            <div class="value value-name"><x-icon name="user" /> {{ $p['full_name'] ?: '-' }}</div>
        </div>
        <div class="field">
            <div class="label">{{ __('Employer') }} <span class="en">{{ t_en('Employer') }}</span></div>
            <div class="value">{{ $emp['name'] ?? '-' }}</div>
        </div>
        <div class="field field-inline">
            <div class="label">{{ __('Status') }} <span class="en">{{ t_en('Status') }}</span></div>
            @if ($st['active'])
                <span class="pill pill-ok">{{ __('Active') }}</span> <span class="status-th">{{ t_en('Active') ?: __('Employed') }}</span>
            @else
                <span class="pill pill-danger">{{ __('Inactive') }}</span> <span class="status-th">{{ $statusText }}</span>
            @endif
        </div>
    </section>

    {{-- ================================================== Alerts --}}
    @if ($alerts)
        <section class="card card-alerts" id="alerts">
            <div class="card-head">
                <span class="card-icon tone-warn"><x-icon name="bell" /></span>
                <div><b>{{ __('Notifications') }}</b><small>{{ __('Documents expiring soon') }}</small></div>
            </div>
            <ul class="alert-list">
                @foreach ($alerts as $a)
                    <li class="level-{{ $a['level'] }}">
                        <span class="name">{{ __($a['label_en']) }}@if (t_en($a['label_en'])) <small>{{ $a['label_en'] }}</small>@endif</span>
                        <span class="when">
                            @if ($a['days_left'] < 0) {{ __('Expired') }} · {{ abs($a['days_left']) }} {{ __('days ago') }}
                            @elseif ($a['days_left'] === 0) {{ __('Expires today') }}
                            @else {{ $a['days_left'] }} {{ __('days left') }}
                            @endif
                            <small>{{ $a['date_end_text'] }}</small>
                        </span>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif

    {{-- ================================================== Passport (screen 3) --}}
    <section class="card card-doc">
        <div class="card-head">
            <span class="card-icon tone-navy"><x-icon name="passport" /></span>
            <div><b>{{ __('Passport No.') }}</b><span class="big">{{ $p['passport']['number'] ?: '-' }}</span></div>
        </div>
        <div class="row-label">
            <span>{{ __('Expiry Date') }} <small>{{ t_en('Expiry Date') }}</small></span>
            <span class="val">{{ $p['passport']['date_end_text'] }}</span>
        </div>
        <div class="remain-block">
            <span class="remain-label">{{ __('Remaining') }}{{ t_en('Remaining') ? ' / Remaining' : '' }}</span>
            <x-remaining :days="$p['passport']['days_left']" :level="$p['passport']['level']" />
        </div>
    </section>

    {{-- ================================================== Work permit --}}
    <section class="card card-doc">
        <div class="card-head">
            <span class="card-icon tone-navy"><x-icon name="work" /></span>
            <div><b>{{ __('Work Permit') }}</b><small>{{ t_en('Work Permit') }}</small></div>
        </div>
        <dl class="kv">
            <dt>{{ __('No.') }} <small>{{ t_en('No.') }}</small></dt><dd>{{ $p['work_permit']['number'] ?: '-' }}</dd>
            <dt>{{ __('Start Date') }} <small>{{ t_en('Start Date') }}</small></dt><dd>{{ $p['work_permit']['date_start_text'] }}</dd>
            <dt>{{ __('Expiry Date') }} <small>{{ t_en('Expiry Date') }}</small></dt><dd>{{ $p['work_permit']['date_end_text'] }}</dd>
            <dt>{{ __('Remaining') }} <small>{{ t_en('Remaining') }}</small></dt><dd><x-remaining size="sm" :days="$p['work_permit']['days_left']" :level="$p['work_permit']['level']" /></dd>
        </dl>
    </section>

    {{-- ================================================== Visa --}}
    <section class="card card-doc">
        <div class="card-head">
            <span class="card-icon tone-navy"><x-icon name="visa" /></span>
            <div><b>{{ __('Visa') }}</b><small>{{ t_en('Visa') }}</small></div>
        </div>
        <dl class="kv">
            <dt>{{ __('No.') }} <small>{{ t_en('No.') }}</small></dt><dd>{{ $p['visa']['number'] ?: '-' }}</dd>
            <dt>{{ __('Start Date') }} <small>{{ t_en('Start Date') }}</small></dt><dd>{{ $p['visa']['date_start_text'] }}</dd>
            <dt>{{ __('Expiry Date') }} <small>{{ t_en('Expiry Date') }}</small></dt><dd>{{ $p['visa']['date_end_text'] }}</dd>
            <dt>{{ __('Remaining') }} <small>{{ t_en('Remaining') }}</small></dt><dd><x-remaining size="sm" :days="$p['visa']['days_left']" :level="$p['visa']['level']" /></dd>
        </dl>
    </section>

    {{-- ================================================== 90 day (screen 4) --}}
    <section class="card card-doc">
        <div class="card-head">
            <span class="card-icon tone-navy"><x-icon name="passport" /></span>
            <div><b>{{ __('90 Day Notify') }}</b><small>{{ t_en('90 Day Notify') }}</small></div>
            <x-icon name="alarm" class="head-right" />
        </div>
        <div class="two-col">
            <div>
                <div class="label">{{ __('Remaining') }}{{ t_en('Remaining') ? ' / Remaining' : '' }}</div>
                <x-remaining :days="$p['ninety_day']['days_left']" :level="$p['ninety_day']['level']" />
            </div>
            <div class="text-end">
                <div class="label">{{ __('Expiry Date') }}</div>
                <div class="val-lg">{{ $p['ninety_day']['date_end_text'] }}</div>
                @if ($p['ninety_day']['tm_number'])<div class="muted small">TM. {{ $p['ninety_day']['tm_number'] }}</div>@endif
            </div>
        </div>
    </section>

    {{-- ================================================== Address --}}
    <section class="card card-doc">
        <div class="card-head">
            <span class="card-icon tone-navy"><x-icon name="home" /></span>
            <div><b>{{ __('Report Address') }}</b><small>{{ t_en('Report Address') }}</small></div>
        </div>
        <div class="label">{{ __('Status') }}{{ t_en('Status') ? ' / Status' : '' }}</div>
        <div class="status-pair">
            <span class="opt {{ $st['active'] ? 'on-ok' : '' }}"><x-icon name="check-box" /> <span>{{ __('Active') }}@if (t_en('Active'))<br><small>Active</small>@endif</span></span>
            <span class="opt {{ ! $st['active'] ? 'on-danger' : '' }}"><x-icon name="x-box" /> <span>{{ __('Inactive') }}@if (t_en('Inactive'))<br><small>Inactive</small>@endif</span></span>
        </div>
        <div class="label mt">{{ __('Address') }}{{ t_en('Address') ? ' / Address' : '' }}</div>
        @if ($emp && ($emp['address']['text_en'] ?? '' || $emp['address']['text_th'] ?? ''))
            <div class="addr">{{ $emp['address']['text_en'] ?: $emp['address']['text_th'] }}</div>
            @if ($emp['address']['text_th'] && $emp['address']['text_th'] !== $emp['address']['text_en'])<div class="addr en">{{ $emp['address']['text_th'] }}</div>@endif
        @else
            <div class="muted">-</div>
        @endif
    </section>

    {{-- ================================================== Download --}}
    <section class="card card-doc card-download">
        <div class="card-head">
            <span class="card-icon tone-navy"><x-icon name="download-doc" /></span>
            <div><b>{{ __('Download Document') }}</b><small>{{ t_en('Download Document') }}</small></div>
        </div>
        <a href="{{ route('documents') }}" class="btn btn-accent btn-block">
            {{ __('Download') }}{{ t_en('Download') ? ' / Download' : '' }}
            @if ($docs)<span class="count">{{ count($docs) }}</span>@endif
        </a>
    </section>

    <p class="updated">{{ __('Data as of') }} {{ \Carbon\Carbon::parse($p['generated_at'])->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
        · <a href="{{ route('home', ['refresh' => 1]) }}">{{ __('Refresh') }}</a></p>
@endsection
