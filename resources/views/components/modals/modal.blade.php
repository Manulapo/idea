@props([
    'name' => 'create-idea',
    'title' => 'Create an idea',
])

<div
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-xs"
    style="display: none"
    x-data="{ show: false, name: '{{ $name }}' }"
    x-show="show"
    @open-modal.window="if ($event.detail === name) show = true"
    @close-modal.window="show = false"
    @click.self="show = false"
    x-transition:enter="ease-out duration-200"
    x-transition:enter-start="opacity-0 -translate-y-4 -translate-x-4"
    x-transition:leave-start="opacity-100"
    x-transition:enter-end="opacity-100"
    x-transition:leave="duration-150 ease-in"
    x-transition:leave-end="opacity-0"
    role="dialog"
    aria-modal="true"
    aria-labelledby="modal-{{ $name }}-title"
    aria-hidden="true"
    tabindex="-1"
>
    <x-card
        is="div"
        class="w-full max-w-2xl max-h-[80dvh] overflow-auto relative"
    >
        <div>
            <h2
                id="modal-{{ $name }}-title"
                class="text-xl font-bold mb-4"
            >
                {{ $title }}
            </h2>
            <button
                type="button"
                @click="show = false"
                aria-label="Close modal"
                class="absolute top-4 right-4 text-muted-foreground hover:text-foreground"
            >
                <iconify-icon
                    icon="lucide:x"
                    width="24"
                    height="24"
                ></iconify-icon>
            </button>
        </div>
        <div>
            {{ $slot }}
        </div>
    </x-card>
</div>
