@extends('layouts.app')
@section('title', __('Profile'))
@section('topbar')@include('partials.topbar')@endsection
@section('nav')@include('partials.nav')@endsection

@section('content')
    @php
        $st = $p['status']; $emp = $p['employer'];
        $nat = __($p['nationality']['name_en'] ?? '') ?: ($p['nationality']['name_en'] ?? '-');
        $sex = $p['sex'] === 'female' ? __('Female') : (in_array($p['sex'], ['male', 'man'], true) ? __('Male') : '-');
        $age = age_parts($p['birthday'] ?? null);
        $statusText = __($st['label_en'] ?? '') ?: ($st['label_en'] ?? '-');
        $phone = preg_replace('/\D+/', '', $emp['phone'] ?? '');
    @endphp
    <section class="card card-identity">
        <div class="identity-icon" aria-hidden="true"><x-icon name="worker" /></div>
        <div class="value value-name center">{{ $p['full_name'] ?: '-' }}</div>
        <div class="muted center">{{ $nat }}@if (t_en($p['nationality']['name_en'] ?? '')) · {{ $p['nationality']['name_en'] }}@endif</div>
        <div class="center mt-sm">
            @if ($st['active'])
                <span class="pill pill-ok">{{ __('Active') }}</span> <span class="status-th">{{ __('Employed') }}</span>
            @else
                <span class="pill pill-danger">{{ __('Inactive') }}</span> <span class="status-th">{{ $statusText }}</span>
            @endif
        </div>
    </section>

    <section class="card">
        <div class="card-head"><span class="card-icon tone-navy"><x-icon name="user" /></span><div><b>{{ __('Personal') }}</b><small>{{ t_en('Personal') }}</small></div></div>
        <dl class="kv">
            <dt>{{ __('Passport No.') }} <small>{{ t_en('Passport No.') }}</small></dt><dd>{{ $p['passport_number'] ?: '-' }}</dd>
            <dt>{{ __('Birthday') }} <small>{{ t_en('Birthday') }}</small></dt><dd>{{ $p['birthday_text'] }}@if ($age) <small class="muted">({{ $age[0] }} {{ __('years') }} {{ $age[1] }} {{ __('months') }})</small>@endif</dd>
            <dt>{{ __('Sex') }} <small>{{ t_en('Sex') }}</small></dt><dd>{{ $sex }}</dd>
            <dt>{{ __('Nationality') }} <small>{{ t_en('Nationality') }}</small></dt><dd>{{ $nat }}</dd>
            @if ($p['labour_number'])<dt>{{ __('Labour No.') }} <small>{{ t_en('Labour No.') }}</small></dt><dd>{{ $p['labour_number'] }}</dd>@endif
        </dl>
    </section>

    <section class="card">
        <div class="card-head"><span class="card-icon tone-navy"><x-icon name="work" /></span><div><b>{{ __('Employment') }}</b><small>{{ t_en('Employment') }}</small></div></div>
        <dl class="kv">
            <dt>{{ __('Employer') }} <small>{{ t_en('Employer') }}</small></dt><dd>{{ $emp['name'] ?? '-' }}</dd>
            @if ($emp && trim($emp['type'] ?? '', " -"))<dt>{{ __('Business') }} <small>{{ t_en('Business') }}</small></dt><dd>{{ $emp['type'] }}</dd>@endif
            @if (strlen($phone) >= 6)<dt>{{ __('Phone') }} <small>{{ t_en('Phone') }}</small></dt><dd><a href="tel:{{ $phone }}">{{ $emp['phone'] }}</a></dd>@endif
            <dt>{{ __('Agency') }} <small>{{ t_en('Agency') }}</small></dt><dd>{{ $p['agency']['name'] ?? '-' }}</dd>
            <dt>{{ __('Start Date') }} <small>{{ t_en('Start Date') }}</small></dt><dd>{{ $st['job_date_start_text'] }}</dd>
            <dt>{{ __('Status') }} <small>{{ t_en('Status') }}</small></dt><dd>{{ $statusText }}</dd>
        </dl>
    </section>

    <section class="card">
        <div class="card-head"><span class="card-icon tone-navy"><x-icon name="visa" /></span><div><b>{{ __('Visa') }}</b><small>{{ t_en('Visa') }}</small></div></div>
        <dl class="kv">
            <dt>{{ __('No.') }} <small>{{ t_en('No.') }}</small></dt><dd>{{ $p['visa']['number'] ?: '-' }}</dd>
            <dt>{{ __('Arrival') }} <small>{{ t_en('Arrival') }}</small></dt><dd>{{ $p['visa']['date_in_text'] }}</dd>
            <dt>{{ __('Start') }} <small>{{ t_en('Start') }}</small></dt><dd>{{ $p['visa']['date_start_text'] }}</dd>
            <dt>{{ __('Expiry') }} <small>{{ t_en('Expiry') }}</small></dt><dd>{{ $p['visa']['date_end_text'] }}</dd>
            <dt>{{ __('Remaining') }} <small>{{ t_en('Remaining') }}</small></dt><dd><x-remaining size="sm" :days="$p['visa']['days_left']" :level="$p['visa']['level']" /></dd>
        </dl>
    </section>

    <div class="card lang-card">
        <div class="label"><x-icon name="globe" /> {{ __('Language') }}</div>
        <x-lang-switch />
    </div>

    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit" class="btn btn-outline btn-block"><x-icon name="logout" /> {{ __('Log out') }}</button>
    </form>
@endsection
