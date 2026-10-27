@extends('layouts.app')
@section('title', 'เกิดข้อผิดพลาด / Error')
@section('screen-class', 'screen-login')
@section('content')
    <div class="card center">
        <h2>เกิดข้อผิดพลาด / Something went wrong</h2>
        <p class="muted">กรุณาลองใหม่อีกครั้ง / Please try again.</p>
        <a href="{{ url('/') }}" class="btn btn-primary btn-block mt">กลับหน้าแรก / Home</a>
    </div>
@endsection
