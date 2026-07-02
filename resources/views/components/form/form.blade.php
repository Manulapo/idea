@php
    $method = strtoupper($attributes->get('method', 'GET'));
    $formMethod = $method === 'GET' ? 'GET' : 'POST';
@endphp

<form {{ $attributes->merge(['class' => 'max-w-2xl mx-auto space-y-6']) }} method="{{ $formMethod }}">
    @if ($method !== 'GET')
        @csrf
        @if (!in_array($method, ['GET', 'POST']))
            @method($method)
        @endif
    @endif

    {{ $slot }}
</form>
