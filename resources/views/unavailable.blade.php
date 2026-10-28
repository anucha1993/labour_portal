@extends('layouts.app')
@section('title', __('Sorry'))
@section('screen-class', 'screen-login')

@section('content')
    <div class="card center">
        <div class="identity-icon tone-warn" aria-hidden="true"><x-icon name="alert" /></div>
        <h2 class="mt-sm">{{ __('Sorry') }}</h2>
        <p class="muted">{{ $message }}</p>
        <a href="{{ url()->current() }}" class="btn btn-primary btn-block mt">{{ __('Try again') }}</a>
        <form method="POST" action="{{ route('logout') }}" class="mt-sm">@csrf<button class="btn btn-outline btn-block">{{ __('Log out') }}</button></form>
    </div>
@endsection
