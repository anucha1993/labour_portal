@extends('layouts.app')
@section('title', 'เข้าสู่ระบบ / Log in')
@section('body-class', 'is-login')
@section('screen-class', 'screen-login')

@section('content')
    <div class="login-hero">
        <div class="login-badge" aria-hidden="true"><x-icon name="worker" /></div>
        <h1>Log in</h1>
        <p class="login-sub">ตรวจสอบเอกสารแรงงานของคุณ<br>Check your work documents</p>
    </div>

    <form method="POST" action="{{ route('login.post') }}" class="login-form" novalidate data-submit-once>
        @csrf
        <label class="pill-input {{ $errors->has('passport') ? 'is-invalid' : '' }}">
            <x-icon name="user" />
            <input type="text" name="passport" value="{{ old('passport') }}" placeholder="User name · เลขที่ Passport"
                   autocomplete="username" autocapitalize="characters" spellcheck="false" inputmode="text" required autofocus>
        </label>

        <label class="pill-input {{ $errors->has('password') ? 'is-invalid' : '' }}">
            <x-icon name="key" />
            <input type="password" name="password" placeholder="Password · วันเกิด ddmmyyyy" autocomplete="current-password"
                   inputmode="numeric" pattern="[0-9/]*" maxlength="10" required data-password>
            <button type="button" class="eye" data-toggle-password aria-label="แสดง/ซ่อนรหัสผ่าน">
                <x-icon name="eye-off" class="icon-eye-off" /><x-icon name="eye" class="icon-eye" />
            </button>
        </label>

        @if ($errors->any())
            <div class="form-error" role="alert"><x-icon name="alert" /> {{ $errors->first() }}</div>
        @endif

        <button type="submit" class="btn btn-primary btn-block">เข้าสู่ระบบ / Log in</button>

        <div class="login-hint">
            <div><b>User:</b> เลขที่ Passport <span class="en">/ Passport number</span></div>
            <div><b>Pass:</b> วันเกิด ddmmyyyy <span class="en">/ Birthday, e.g. 06121997</span></div>
        </div>
    </form>

    <p class="login-foot">© {{ date('Y') }} The Importer 168 Co., Ltd.</p>
@endsection
