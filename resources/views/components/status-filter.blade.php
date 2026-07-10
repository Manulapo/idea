@props(['statusCounts'])

<div
    class="mt-6"
    aria-label="Filter ideas by status"
>
    <a
        href="/ideas"
        class="btn {{ request('status') === null ? 'btn-primary' : 'btn-outlined' }}"
        aria-current="{{ request('status') === null ? 'page' : 'false' }}"
    >All</a>
    @foreach (App\IdeaStatus::cases() as $status)
        <a
            href="/ideas?status={{ $status->value }}"
            class="btn {{ request('status') === $status->value ? 'btn-primary' : 'btn-outlined' }}"
            aria-current="{{ request('status') === $status->value ? 'page' : 'false' }}"
        >{{ $status->label() }}
            <span
                class="text-xs pl-3"
                aria-label="{{ $statusCounts->get($status->value) }} ideas"
            >{{ $statusCounts->get($status->value) }}</span></a>
    @endforeach
</div>
