@extends('layouts.app')
@section('title', 'ไม่สามารถเชื่อมต่อได้')
@section('screen-class', 'screen-login')

@section('content')
    <div class="card center">
        <div class="identity-icon tone-warn" aria-hidden="true"><x-icon name="alert" /></div>
        <h2 class="mt-sm">ขออภัย / Sorry</h2>
        <p class="muted">{{ $message }}</p>
        <a href="{{ url()->current() }}" class="btn btn-primary btn-block mt">ลองใหม่ / Try again</a>
        <form method="POST" action="{{ route('logout') }}" class="mt-sm">@csrf<button class="btn btn-outline btn-block">ออกจากระบบ / Log out</button></form>
    </div>
@endsection
