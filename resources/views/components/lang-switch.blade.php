@props(['style' => 'pills'])
@php $current = app()->getLocale(); $locales = portal_locales(); @endphp
@if (count($locales) > 1)
    @if ($style === 'select')
        <form method="GET" action="{{ url()->current() }}" class="lang-select-form" title="{{ __('Language') }}">
            <x-icon name="globe" />
            <select name="lang" class="lang-select" aria-label="{{ __('Language') }}" onchange="this.form.submit()">
                @foreach ($locales as $code => $name)
                    <option value="{{ $code }}" {{ $code === $current ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </form>
    @else
        <div class="lang-pills" role="group" aria-label="{{ __('Language') }}">
            @foreach ($locales as $code => $name)
                <a href="{{ request()->fullUrlWithQuery(['lang' => $code]) }}" class="{{ $code === $current ? 'active' : '' }}" lang="{{ $code }}">{{ $name }}</a>
            @endforeach
        </div>
    @endif
@endif
