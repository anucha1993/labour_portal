@extends('layouts.app')
@section('title', 'ข้อมูลของฉัน / Profile')
@section('topbar')@include('partials.topbar')@endsection
@section('nav')@include('partials.nav')@endsection

@section('content')
    @php $st = $p['status']; $emp = $p['employer']; @endphp
    <section class="card card-identity">
        <div class="identity-icon" aria-hidden="true"><x-icon name="worker" /></div>
        <div class="value value-name center">{{ $p['full_name'] ?: '-' }}</div>
        <div class="muted center">{{ $p['nationality']['name_en'] }} · {{ $p['nationality']['name_th'] }}</div>
        <div class="center mt-sm">
            @if ($st['active'])
                <span class="pill pill-ok">Active</span> <span class="status-th">จ้างงานอยู่</span>
            @else
                <span class="pill pill-danger">Inactive</span> <span class="status-th">{{ $st['label_th'] }} / {{ $st['label_en'] }}</span>
            @endif
        </div>
    </section>

    <section class="card">
        <div class="card-head"><span class="card-icon tone-navy"><x-icon name="user" /></span><div><b>Personal</b><small>ข้อมูลส่วนตัว</small></div></div>
        <dl class="kv">
            <dt>Passport No. <small>เลขที่หนังสือเดินทาง</small></dt><dd>{{ $p['passport_number'] ?: '-' }}</dd>
            <dt>Birthday <small>วันเกิด</small></dt><dd>{{ $p['birthday_text'] }}@if ($p['age_text'] !== '-') <small class="muted">({{ $p['age_text'] }})</small>@endif</dd>
            <dt>Sex <small>เพศ</small></dt><dd>{{ $p['sex_label'] }}</dd>
            <dt>Nationality <small>สัญชาติ</small></dt><dd>{{ $p['nationality']['name_th'] }} / {{ $p['nationality']['name_en'] }}</dd>
            @if ($p['labour_number'])<dt>Labour No. <small>เลขที่แรงงาน</small></dt><dd>{{ $p['labour_number'] }}</dd>@endif
        </dl>
    </section>

    <section class="card">
        <div class="card-head"><span class="card-icon tone-navy"><x-icon name="work" /></span><div><b>Employment</b><small>การจ้างงาน</small></div></div>
        <dl class="kv">
            <dt>Employer <small>นายจ้าง</small></dt><dd>{{ $emp['name'] ?? '-' }}</dd>
            @if ($emp && trim($emp['type'] ?? '', " -"))<dt>Business <small>ประเภทกิจการ</small></dt><dd>{{ $emp['type'] }}</dd>@endif
            @if ($emp && preg_match('/\d{6,}/', preg_replace('/\D+/', '', $emp['phone'] ?? '')))<dt>Phone <small>โทรศัพท์</small></dt><dd><a href="tel:{{ preg_replace('/\D+/', '', $emp['phone']) }}">{{ $emp['phone'] }}</a></dd>@endif
            <dt>Agency <small>เอเจนซี่</small></dt><dd>{{ $p['agency']['name'] ?? '-' }}</dd>
            <dt>Start Date <small>วันเริ่มงาน</small></dt><dd>{{ $st['job_date_start_text'] }}</dd>
            <dt>Status <small>สถานะ</small></dt><dd>{{ $st['label_th'] }} / {{ $st['label_en'] }}</dd>
        </dl>
    </section>

    <section class="card">
        <div class="card-head"><span class="card-icon tone-navy"><x-icon name="visa" /></span><div><b>Visa</b><small>วีซ่า</small></div></div>
        <dl class="kv">
            <dt>No. <small>เลขที่</small></dt><dd>{{ $p['visa']['number'] ?: '-' }}</dd>
            <dt>Arrival <small>วันที่เข้าประเทศ</small></dt><dd>{{ $p['visa']['date_in_text'] }}</dd>
            <dt>Start <small>วันเริ่มต้น</small></dt><dd>{{ $p['visa']['date_start_text'] }}</dd>
            <dt>Expiry <small>วันสิ้นสุด</small></dt><dd>{{ $p['visa']['date_end_text'] }}</dd>
            <dt>Remaining <small>คงเหลือ</small></dt><dd><x-remaining size="sm" :days="$p['visa']['days_left']" :level="$p['visa']['level']" /></dd>
        </dl>
    </section>

    <form method="POST" action="{{ route('logout') }}" class="logout-form">
        @csrf
        <button type="submit" class="btn btn-outline btn-block"><x-icon name="logout" /> ออกจากระบบ / Log out</button>
    </form>
@endsection
