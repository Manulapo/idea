@props(['show' => 'showMenu'])

<x-card
    is="ul"
    x-cloak
    x-show="{{ $show }}"
    x-transition:enter="ease-out duration-150"
    x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
    x-transition:enter-end="opacity-100 translate-y-0 scale-100"
    x-transition:leave="ease-in duration-100"
    x-transition:leave-start="opacity-100 translate-y-0 scale-100"
    x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
    {{ $attributes->merge(['class' => 'menu bg-base-200 rounded-box absolute right-0 top-12 w-52 p-1 shadow-lg z-50 space-y-1']) }}
>
    <ul
        class="dropdown-content menu shadow bg-base-100 rounded-box space-y-2"
        role="menu"
    >
        {{ $slot }}
    </ul>
</x-card>
