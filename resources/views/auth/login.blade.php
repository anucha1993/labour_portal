@extends('layouts.app')
@section('title', __('Log in'))
@section('body-class', 'is-login')
@section('screen-class', 'screen-login')

@section('content')
    <x-lang-switch />

    <div class="login-hero">
        <div class="login-badge" aria-hidden="true"><img src="{{ asset('logo168.png') }}" alt="The Importer 168"></div>
        <h1>{{ __('Log in') }}</h1>
        <p class="login-sub">{{ __('Check your work documents') }}@if (t_en('Check your work documents'))<br><span class="en">Check your work documents</span>@endif</p>
    </div>

    <form method="POST" action="{{ route('login.post') }}" class="login-form" novalidate data-submit-once data-loading-text="{{ __('Signing in…') }}">
        @csrf
        <label class="pill-input {{ $errors->has('passport') ? 'is-invalid' : '' }}">
            <x-icon name="user" />
            <input type="text" name="passport" value="{{ old('passport') }}" placeholder="{{ __('Passport number or name') }}"
                   autocomplete="username" autocapitalize="characters" spellcheck="false" inputmode="text" required autofocus>
        </label>

        <label class="pill-input {{ $errors->has('password') ? 'is-invalid' : '' }}">
            <x-icon name="key" />
            <input type="password" name="password" placeholder="{{ __('Birthday (ddmmyyyy)') }}" autocomplete="current-password"
                   inputmode="numeric" pattern="[0-9/]*" maxlength="10" required data-password>
            <button type="button" class="eye" data-toggle-password aria-label="{{ __('Show or hide password') }}">
                <x-icon name="eye-off" class="icon-eye-off" /><x-icon name="eye" class="icon-eye" />
            </button>
        </label>

        @if ($errors->any())
            <div class="form-error" role="alert"><x-icon name="alert" /> {{ $errors->first() }}</div>
        @endif

        <button type="submit" class="btn btn-primary btn-block">{{ __('Log in') }}</button>

        <div class="login-hint">
            <div><b>{{ __('User') }}:</b> {{ __('your passport number, or your name as in the passport (spaces do not matter, e.g. MYO MIN OO or myominoo)') }}</div>
            <div><b>{{ __('Password') }}:</b> {{ __('your birthday as day-month-year (ddmmyyyy), e.g. 4 July 1979 → 04071979') }}</div>
        </div>
    </form>

    <p class="login-foot">© {{ date('Y') }} The Importer 168 Co., Ltd.</p>
@endsection
