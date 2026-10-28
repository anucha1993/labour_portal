@extends('layouts.app')
@section('title', __('Maintenance'))
@section('screen-class', 'screen-login')
@section('content')
    <div class="card center">
        <h2>{{ __('Maintenance') }}</h2>
        <p class="muted">{{ __('We are doing maintenance, please come back shortly.') }}</p>
    </div>
@endsection
