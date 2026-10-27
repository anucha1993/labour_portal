<nav class="bottom-nav" aria-label="เมนู">
    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">
        <x-icon name="home" /><span>หน้าแรก<br><small>Home</small></span>
    </a>
    <a href="{{ route('documents') }}" class="{{ request()->routeIs('documents*') ? 'active' : '' }}">
        <x-icon name="docs" /><span>เอกสาร<br><small>Documents</small></span>
    </a>
    <a href="{{ route('profile') }}" class="{{ request()->routeIs('profile') ? 'active' : '' }}">
        <x-icon name="user-circle" /><span>ข้อมูลของฉัน<br><small>Profile</small></span>
    </a>
</nav>
