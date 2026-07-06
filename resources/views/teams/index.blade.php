<x-layout.layout>
    <div class="flex justify-between items-center">
        <x-header
            title="Teams"
            description="View the teams you are part of."
        />
        <x-card
            class="pointer my-10 text-left bg-primary btn flex items-center gap-2"
            is="button"
            type="button"
            x-data
            @click="$dispatch('open-modal', 'create-team')"
            data-test="create-idea-button"
            aria-label="Create a new idea"
            aria-haspopup="dialog"
        >
            <iconify-icon
                icon="lucide:plus"
                width="24"
                height="24"
            ></iconify-icon>
            <p>Create Team</p>
        </x-card>
    </div>
    {{-- team Badge --}}
    <x-card
        class="pointer my-10 text-left flex flex-col space-y-2"
        is="div"
    >
        <h3 class="font-bold mb-3">Teams</h3>
        <div class="flex gap-2 flex-wrap">
            @forelse ($teams as $team)
                <x-teams.team-badge :team="$team" />
            @empty
                <p class="text-sm text-foreground/80">You are not part of any teams.</p>
            @endforelse
        </div>
    </x-card>

    <x-modals.team-modal
        name="create-team"
        title="Create Team"
        :users="$users"
    />
</x-layout.layout>
