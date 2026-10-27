@extends('layouts.app')
@section('title', 'หมดเวลา / Session expired')
@section('screen-class', 'screen-login')
@section('content')
    <div class="card center">
        <h2>หมดเวลา / Session expired</h2>
        <p class="muted">หน้านี้เปิดค้างไว้นานเกินไป กรุณาลองใหม่<br>The page was open too long, please try again.</p>
        <a href="{{ url('/') }}" class="btn btn-primary btn-block mt">ลองใหม่ / Try again</a>
    </div>
@endsection
