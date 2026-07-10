@props(['idea', 'showUser' => true])

<x-card
    href="{{ '/ideas/' . $idea->id }}"
    role="listitem"
    aria-label="View idea: {{ $idea->title }}"
>
    @if ($idea->image_path)
        <div class="rounded-lg overflow-hidden -mx-4 -mt-4">
            <img
                src="{{ asset('storage/' . $idea->image_path) }}"
                alt="{{ $idea->title }}"
                class="w-full h-48 object-cover mb-4"
            >
        </div>
    @endif
    <h3 class="text-foreground text-lg mb-2">{{ $idea->title }}</h3>
    <x-status-label :status="$idea->status" />
    <p class="text-muted-foreground line-clamp-3 mt-5 h-15">{{ $idea->description }}</p>
    <div class="flex justify-between items-center mt-4">
        @if ($showUser)
            <div class="flex items-center gap-1">
                <x-user-avatar
                    :user="$idea->user"
                    size="6"
                />
                <span class="ml-2 text-sm text-muted-foreground">{{ $idea->user->name }}</span>
                {{-- <span
            <span class="text-muted-foreground text-sm">in</span>
                class="ml-2 text-sm text-muted-foreground">{{ $idea->user->teams->first()?->name ?? 'No Team' }}</span> --}}
            </div>
        @endif
        <div class="mt-4 text-sm text-muted-foreground/50">
            <time datetime="{{ $idea->created_at->toIso8601String() }}">{{ $idea->created_at->diffForHumans() }}</time>
        </div>
    </div>
</x-card>
