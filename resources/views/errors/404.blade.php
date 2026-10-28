@extends('layouts.app')
@section('title', __('Page not found'))
@section('screen-class', 'screen-login')
@section('content')
    <div class="card center">
        <h2>404</h2>
        <p class="muted">{{ __('Page not found') }}</p>
        <a href="{{ url('/') }}" class="btn btn-primary btn-block mt">{{ __('Home') }}</a>
    </div>
@endsection
