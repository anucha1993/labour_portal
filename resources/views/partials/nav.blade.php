<nav class="bottom-nav" aria-label="{{ __('Home') }}">
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
        <x-icon name="home" /><span>{{ __('Home') }}@if (t_en('Home'))<br><small>Home</small>@endif</span>
    </a>
    <a href="{{ route('documents') }}" class="{{ request()->routeIs('documents*') ? 'active' : '' }}">
        <x-icon name="docs" /><span>{{ __('Documents') }}@if (t_en('Documents'))<br><small>Documents</small>@endif</span>
    </a>
    <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">
        <x-icon name="user-circle" /><span>{{ __('Profile') }}@if (t_en('Profile'))<br><small>Profile</small>@endif</span>
    </a>
</nav>
