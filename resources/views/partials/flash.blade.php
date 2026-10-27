@foreach (['success' => 'ok', 'error' => 'danger', 'info' => 'info'] as $key => $tone)
    @if (session($key))
        <div class="flash flash-{{ $tone }}" role="alert">
            <x-icon :name="$tone === 'ok' ? 'check-circle' : ($tone === 'danger' ? 'alert' : 'info')" />
            <span>{{ session($key) }}</span>
        </div>
    @endif
@endforeach
