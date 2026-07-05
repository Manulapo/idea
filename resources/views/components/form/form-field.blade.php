@props([
    'name' => '',
    'label' => '',
    'type' => 'text',
    'value' => null,
])

<div class="space-y-3">
    <label for="{{ $name }}" class="label text-start">{{ $label }}</label>
    @if ($type === 'textarea')
        <textarea name="{{ $name }}" id="{{ $name }}" class="textarea" {{ $attributes }}>{{ old($name, $value) }}</textarea>
    @elseif ($type === 'file')
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" class="input" multiple
            {{ $attributes }} />
    @else
        <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" class="input"
            value="{{ old($name, $value) }}" {{ $attributes }} />
    @endif
</div>
@error($name)
    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
@enderror
