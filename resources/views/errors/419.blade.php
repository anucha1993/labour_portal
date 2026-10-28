@extends('layouts.app')
@section('title', __('Session expired'))
@section('screen-class', 'screen-login')
@section('content')
    <div class="card center">
        <h2>{{ __('Session expired') }}</h2>
        <p class="muted">{{ __('The page was open too long, please try again.') }}</p>
        <a href="{{ url('/') }}" class="btn btn-primary btn-block mt">{{ __('Try again') }}</a>
    </div>
@endsection
