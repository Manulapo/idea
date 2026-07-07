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
    <div
        class="pointer my-10 text-left flex flex-col space-y-2"
        is="div"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse ($teams as $team)
                <x-card is="div">
                    <a
                        href="{{ route('teams.show', $team) }}"
                        class="rounded-lg text-xs flex flex-col items-start gap-2"
                    >
                        <div class="font-bold text-lg mb-2">
                            {{ $team->name }}
                        </div>
                        <div class="text-xs text-muted-foreground line-clamp-2 mb-2 h-8">
                            {{ $team->description ?? '' }}
                        </div>

                        <div
                            class=" text-xs text-muted-foreground flex items-center gap-1 border-t border-muted-foreground/20 pt-4 w-full">
                            <iconify-icon
                                icon="lucide:users"
                                class="mr-1"
                                width="16"
                                height="16"
                            ></iconify-icon>
                            {{ $team->users->where('pivot.role', 'member')->count() + $team->users->where('pivot.role', 'admin')->count() }}
                            members
                            @if (
                                $team->users->where('id', $currentUser->id)->first()?->pivot?->role === 'admin' ||
                                    $team->users->where('id', $currentUser->id)->first()?->pivot?->role === 'owner')
                                <span
                                    class="bg-primary text-black px-2 py-1 rounded-md text-[10px] ml-2"
                                    aria-label="You are an admin of this team"
                                >Admin</span>
                            @endif
                        </div>
                    </a>
                </x-card>
            @empty
                <p class="text-sm text-muted-foreground">You are not part of any teams.</p>
            @endforelse
        </div>
    </div>

    <x-modals.team-modal
        name="create-team"
        title="Create Team"
        :users="$users"
    />
</x-layout.layout>
