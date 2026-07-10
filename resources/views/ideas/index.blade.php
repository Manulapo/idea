<x-layout.layout>
    <div class="flex justify-between items-center">
        <x-header
            title="Ideas"
            description="All the ideas for this project"
        />
        <x-card
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

    <x-status-filter :statusCounts="$statusCounts" />

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
                <x-idea-card :idea="$idea" />
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
        :teams="$teams"
    />

</x-layout.layout>
