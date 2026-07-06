@props(['user', 'size' => '24', 'withName' => false])

@php
    $sizeClasses = [
        '6' => 'w-6 h-6',
        '8' => 'w-8 h-8',
        '10' => 'w-10 h-10',
        '12' => 'w-12 h-12',
        '16' => 'w-16 h-16',
        '20' => 'w-20 h-20',
        '24' => 'w-24 h-24',
        '32' => 'w-32 h-32',
    ];

    $avatarSizeClass = $sizeClasses[(string) $size] ?? $sizeClasses['24'];
@endphp
<div class="flex items-center gap-2">
    @if ($withName)
        <span class="mr-2">{{ $user->name }}</span>
    @endif
    @if ($user->image_path)
        <img
            {{ $attributes->merge(['class' => $avatarSizeClass . ' rounded-full object-cover']) }}
            src="{{ asset('storage/' . $user->image_path) }}"
            alt="{{ $user->name }}"
        />
    @else
        {{-- placeholder with User initial --}}
        <div
            {{ $attributes->merge(['class' => $avatarSizeClass . ' rounded-full bg-gray-300 flex items-center justify-center text-white font-bold bg-primary']) }}>
            {{ strtoupper(substr($user->name, 0, 1)) }}
        </div>
    @endif
</div>
