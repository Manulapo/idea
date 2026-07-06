<x-layout.layout>
    <div class="flex justify-between items-center">
        <x-header
            title="Ideas"
            description="All the ideas for this project"
        />
        <x-card
            href="{{ route('ideas.create') }}"
            class="pointer my-10 text-left bg-primary btn flex items-center gap-2"
            is="button"
            type="button"
            x-data
            @click="$dispatch('open-modal', 'create-idea')"
            data-test="create-idea-button"
            aria-label="Create a new idea"
            aria-haspopup="dialog"
        >
            <iconify-icon
                icon="lucide:plus"
                width="24"
                height="24"
            ></iconify-icon>
            <p>Whats the idea?</p>
        </x-card>
    </div>

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

    <section
        class="mt-10"
        aria-labelledby="ideas-heading"
    >
        <h2
            id="ideas-heading"
            class="sr-only"
        >
            @if (request('status'))
                {{ collect(App\IdeaStatus::cases())->firstWhere('value', request('status'))?->label() }} Ideas
            @else
                All Ideas
            @endif
        </h2>
        <div
            class="grid md:grid-cols-2 gap-6"
            role="list"
        >
            @forelse ($ideas as $idea)
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
                    <p class="text-muted-foreground line-clamp-3 mt-5">{{ $idea->description }}</p>
                    <div class="mt-4 text-sm text-muted-foreground/50">
                        <time
                            datetime="{{ $idea->created_at->toIso8601String() }}">{{ $idea->created_at->diffForHumans() }}</time>
                    </div>
                </x-card>
            @empty
                <x-card
                    role="status"
                    aria-live="polite"
                >
                    <p>No ideas yet</p>
                </x-card>
            @endforelse
        </div>
    </section>

    <x-modals.idea-modal
        name="create-idea"
        title="Create Idea"
    />

</x-layout.layout>
