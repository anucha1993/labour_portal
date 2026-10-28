@extends('layouts.app')
@section('title', __('Something went wrong'))
@section('screen-class', 'screen-login')
@section('content')
    <div class="card center">
        <h2>{{ __('Something went wrong') }}</h2>
        <p class="muted">{{ __('Please try again.') }}</p>
        <a href="{{ url('/') }}" class="btn btn-primary btn-block mt">{{ __('Home') }}</a>
    </div>
@endsection
