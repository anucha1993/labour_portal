@extends('layouts.app')
@section('title', 'ไม่พบหน้า / Not found')
@section('screen-class', 'screen-login')
@section('content')
    <div class="card center">
        <h2>404</h2>
        <p class="muted">ไม่พบหน้าที่ต้องการ / Page not found</p>
        <a href="{{ url('/') }}" class="btn btn-primary btn-block mt">กลับหน้าแรก / Home</a>
    </div>
@endsection
