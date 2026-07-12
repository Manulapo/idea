@props(['idea', 'showUser' => true, 'user' => null, 'showTeam' => true])

@php
    $viewerId = $user?->id ?? auth()->id();
@endphp

<x-card
    is="div"
    class="cursor-pointer"
    onclick="window.location='{{ route('ideas.show', $idea->id) }}'"
    onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); window.location='{{ route('ideas.show', $idea->id) }}'; }"
    tabindex="0"
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
    <p class="text-muted-foreground line-clamp-2 mt-5 h-10">{{ $idea->description }}</p>
    <div
        class="flex {{ $showUser && $idea->user && $idea->user->id !== $viewerId ? 'justify-between' : 'justify-end' }} items-center mt-4">
        @if ($showUser && $idea->user && $idea->user->id !== $viewerId)
            <div class="flex items-center gap-1">
                <x-user-avatar
                    :user="$idea->user"
                    size="6"
                />
                <span class="ml-2 text-sm text-muted-foreground">{{ $idea->user->name }}</span>
                @if ($showTeam)
                    <span class="text-muted-foreground text-sm">in</span>
                    <span class="text-sm text-muted-foreground">
                        @if ($idea->team)
                            <a
                                href="{{ route('teams.show', $idea->team->id) }}"
                                class="text-sm text-white/70 hover:underline"
                                onclick="event.stopPropagation()"
                            >
                                {{ $idea->team->name }}
                            </a>
                        @endif
                    </span>
                @endif
            </div>
        @endif
        <div class="mt-4 text-sm text-muted-foreground/50 flex ">
            <time
                datetime="{{ $idea->created_at->toIso8601String() }}">{{ $idea->created_at->diffForHumans() }}</time>
        </div>
    </div>
</x-card>
