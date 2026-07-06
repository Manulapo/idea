@props([
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => null,
])

<div class="space-y-3">
    <label
        for="{{ $name }}"
        class="label text-start"
    >{{ $label }}</label>
    @if ($type === 'textarea')
        <textarea
            {{ $attributes->merge(['class' => 'textarea']) }}
            name="{{ $name }}"
            id="{{ $name }}"
        >{{ old($name, $value) }}</textarea>
    @elseif ($type === 'file')
        <input
            {{ $attributes->merge(['class' => 'text-foreground']) }}
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
        />
    @elseif ($type === 'select')
        <select
            {{ $attributes->merge(['class' => 'select']) }}
            name="{{ $name }}"
            id="{{ $name }}"
        >
            {{ $slot }}
        </select>
    @else
        <input
            {{ $attributes->merge(['class' => 'input']) }}
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $name }}"
            value="{{ old($name, $value) }}"
        />
    @endif
</div>
@error($name)
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
@enderror
