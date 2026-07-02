@props([
    'name' => '',
    'label' => '',
    'type' => 'text',
])

<div class="space-y-3">
    <label for="{{ $name }}" class="label text-start">{{ $label }}</label>
    <input type="{{ $type }}" name="{{ $name }}" id="{{ $name }}" class="input"
        value="{{ old($name, '') }}" {{ $attributes }} />
    @error($name)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
