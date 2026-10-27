@extends('layouts.app')
@section('title', 'หน้าแรก / Home')
@section('topbar')@include('partials.topbar')@endsection
@section('nav')@include('partials.nav')@endsection

@section('content')
    @php
        $st = $p['status'];
        $emp = $p['employer'];
        $alerts = $p['alerts'] ?? [];
        $docs = $p['documents'] ?? [];
    @endphp

    {{-- ================================================== Identity (mockup screen 2) --}}
    <section class="card card-identity">
        <div class="identity-icon" aria-hidden="true"><x-icon name="worker" /></div>
        <div class="field">
            <div class="label">Name <span class="th">ชื่อ-นามสกุล</span></div>
            <div class="value value-name"><x-icon name="user" /> {{ $p['full_name'] ?: '-' }}</div>
        </div>
        <div class="field">
            <div class="label">Employer <span class="th">นายจ้าง</span></div>
            <div class="value">{{ $emp['name'] ?? '-' }}</div>
        </div>
        <div class="field field-inline">
            <div class="label">Status <span class="th">สถานะ</span></div>
            @if ($st['active'])
                <span class="pill pill-ok">Active</span> <span class="status-th">จ้างงานอยู่</span>
            @else
                <span class="pill pill-danger">Inactive</span> <span class="status-th">{{ $st['label_th'] }} / {{ $st['label_en'] }}</span>
            @endif
        </div>
    </section>

    {{-- ================================================== Alerts --}}
    @if ($alerts)
        <section class="card card-alerts" id="alerts">
            <div class="card-head">
                <span class="card-icon tone-warn"><x-icon name="bell" /></span>
                <div><b>Notifications</b><small>แจ้งเตือนเอกสารใกล้หมดอายุ</small></div>
            </div>
            <ul class="alert-list">
                @foreach ($alerts as $a)
                    <li class="level-{{ $a['level'] }}">
                        <span class="name">{{ $a['label_en'] }} <small>{{ $a['label_th'] }}</small></span>
                        <span class="when">
                            @if ($a['days_left'] < 0) หมดอายุแล้ว / Expired {{ abs($a['days_left']) }} d.
                            @elseif ($a['days_left'] === 0) หมดอายุวันนี้ / Today
                            @else เหลือ {{ $a['days_left'] }} วัน / {{ $a['days_left'] }} days left
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
            <div><b>Passport No.</b><span class="big">{{ $p['passport']['number'] ?: '-' }}</span></div>
        </div>
        <div class="row-label">
            <span>Expiry Date <small>วันหมดอายุ</small></span>
            <span class="val">{{ $p['passport']['date_end_text'] }}</span>
        </div>
        <div class="remain-block">
            <span class="remain-label">คงเหลือ / Remaining</span>
            <x-remaining :days="$p['passport']['days_left']" :level="$p['passport']['level']" />
        </div>
    </section>

    {{-- ================================================== Work permit --}}
    <section class="card card-doc">
        <div class="card-head">
            <span class="card-icon tone-navy"><x-icon name="work" /></span>
            <div><b>Work Permit</b><small>ใบอนุญาตทำงาน</small></div>
        </div>
        <dl class="kv">
            <dt>No. <small>เลขที่</small></dt><dd>{{ $p['work_permit']['number'] ?: '-' }}</dd>
            <dt>Start Date <small>วันเริ่มต้น</small></dt><dd>{{ $p['work_permit']['date_start_text'] }}</dd>
            <dt>Expiry Date <small>วันสิ้นสุด</small></dt><dd>{{ $p['work_permit']['date_end_text'] }}</dd>
            <dt>Remaining <small>คงเหลือ</small></dt><dd><x-remaining size="sm" :days="$p['work_permit']['days_left']" :level="$p['work_permit']['level']" /></dd>
        </dl>
    </section>

    {{-- ================================================== Visa --}}
    <section class="card card-doc">
        <div class="card-head">
            <span class="card-icon tone-navy"><x-icon name="visa" /></span>
            <div><b>Visa</b><small>วีซ่า</small></div>
        </div>
        <dl class="kv">
            <dt>No. <small>เลขที่</small></dt><dd>{{ $p['visa']['number'] ?: '-' }}</dd>
            <dt>Start Date <small>วันเริ่มต้น</small></dt><dd>{{ $p['visa']['date_start_text'] }}</dd>
            <dt>Expiry Date <small>วันสิ้นสุด</small></dt><dd>{{ $p['visa']['date_end_text'] }}</dd>
            <dt>Remaining <small>คงเหลือ</small></dt><dd><x-remaining size="sm" :days="$p['visa']['days_left']" :level="$p['visa']['level']" /></dd>
        </dl>
    </section>

    {{-- ================================================== 90 day (screen 4) --}}
    <section class="card card-doc">
        <div class="card-head">
            <span class="card-icon tone-navy"><x-icon name="passport" /></span>
            <div><b>90 Day Notify</b><small>แจ้งที่พัก 90 วัน</small></div>
            <x-icon name="alarm" class="head-right" />
        </div>
        <div class="two-col">
            <div>
                <div class="label">Remain / <span class="th">คงเหลือ</span></div>
                <x-remaining :days="$p['ninety_day']['days_left']" :level="$p['ninety_day']['level']" />
            </div>
            <div class="text-end">
                <div class="label">Expiry Date</div>
                <div class="val-lg">{{ $p['ninety_day']['date_end_text'] }}</div>
                @if ($p['ninety_day']['tm_number'])<div class="muted small">TM. {{ $p['ninety_day']['tm_number'] }}</div>@endif
            </div>
        </div>
    </section>

    {{-- ================================================== Address --}}
    <section class="card card-doc">
        <div class="card-head">
            <span class="card-icon tone-navy"><x-icon name="home" /></span>
            <div><b>Report Address</b><small>แจ้งที่พัก</small></div>
        </div>
        <div class="label">Status / <span class="th">สถานะ</span></div>
        <div class="status-pair">
            <span class="opt {{ $st['active'] ? 'on-ok' : '' }}"><x-icon name="check-box" /> <span>Active<br><small>ใช้งานอยู่</small></span></span>
            <span class="opt {{ ! $st['active'] ? 'on-danger' : '' }}"><x-icon name="x-box" /> <span>Inactive<br><small>ไม่ได้ใช้งาน</small></span></span>
        </div>
        <div class="label mt">Address / <span class="th">ที่อยู่</span></div>
        @if ($emp && ($emp['address']['text_th'] ?? ''))
            <div class="addr">{{ $emp['address']['text_th'] }}</div>
            <div class="addr en">{{ $emp['address']['text_en'] }}</div>
        @else
            <div class="muted">-</div>
        @endif
    </section>

    {{-- ================================================== Download --}}
    <section class="card card-doc card-download">
        <div class="card-head">
            <span class="card-icon tone-navy"><x-icon name="download-doc" /></span>
            <div><b>Download Document</b><small>ดาวน์โหลดเอกสาร</small></div>
        </div>
        <a href="{{ route('documents') }}" class="btn btn-accent btn-block">
            Download / ดาวน์โหลด
            @if ($docs)<span class="count">{{ count($docs) }}</span>@endif
        </a>
    </section>

    <p class="updated">ข้อมูล ณ {{ \Carbon\Carbon::parse($p['generated_at'])->timezone(config('app.timezone'))->format('d/m/Y H:i') }}
        · <a href="{{ route('home', ['refresh' => 1]) }}">รีเฟรช / Refresh</a></p>
@endsection
