@php
    $method = strtoupper($attributes->get('method', 'GET'));
    $formMethod = $method === 'GET' ? 'GET' : 'POST';
@endphp

<form
    {{ $attributes->except('method')->merge(['class' => 'max-w-2xl space-y-6']) }}
    method="{{ $formMethod }}"
>
    @if ($method !== 'GET')
        @csrf
        @if (!in_array($method, ['GET', 'POST']))
            @method($method)
        @endif
    @endif

    {{ $slot }}
</form>
