<x-layout.layout>
    <div class="py-8 mx-auto">
        <div class="flex justify-between items-center gap-2">

            <a
                href="{{ route('teams.index') }}"
                class="flex items-center gap-2"
            >
                <iconify-icon
                    icon="lucide:arrow-left"
                    class="text-foreground"
                    width="24"
                    height="24"
                ></iconify-icon>
                Back to Teams</a>

            <div class="gap-x-3 flex items-center">
                <button
                    x-data
                    class="btn btn-outlined flex items-center"
                    @click="$dispatch('open-modal', 'edit-team')"
                >
                    <iconify-icon
                        icon="lucide:external-link"
                        class="text-foreground mr-2"
                        width="16"
                        height="16"
                    ></iconify-icon>
                    Edit Team</button>
                <x-form.form
                    action="{{ route('teams.destroy', $team->id) }}"
                    method="DELETE"
                    class="inline"
                >
                    <button
                        type="submit"
                        class="btn btn-outlined text-red-400"
                    >Delete</button>
                </x-form.form>
            </div>
        </div>
        <x-header
            :title="$team->name"
            :description="$team->description"
        />

        {{-- table --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            {{-- table content --}}
            <div class="">
                {{-- role --}}
                <x-card
                    class="pointer my-10 text-left flex flex-col space-y-2 min-h-30"
                    is="div"
                >
                    <h3 class="font-bold mb-3">Admin</h3>
                    <div class="flex gap-2 flex-wrap">
                        @forelse ($admins as $user)
                            <x-user-badge
                                :user="$user"
                                :viewerRole="$currentUserRole"
                                :targetRole="\App\TeamRole::from($user->pivot->role)"
                                :withOptions="$currentUserRole->canManageUsers()"
                                :team="$team"
                            />
                        @empty
                            <p class="text-sm text-muted-foreground">No admins yet.</p>
                        @endforelse
                    </div>
                </x-card>
            </div>
            <div class="">
                <x-card
                    class="pointer my-10 text-left flex flex-col space-y-2 min-h-30"
                    is="div"
                >
                    <h3 class="font-bold mb-3">Participants</h3>
                    <div class="flex gap-2 flex-wrap">
                        @forelse ($members as $user)
                            <x-user-badge
                                :user="$user"
                                :viewerRole="$currentUserRole"
                                :targetRole="\App\TeamRole::from($user->pivot->role)"
                                :withOptions="$currentUserRole->canManageUsers()"
                                :team="$team"
                            />
                        @empty
                            <p class="text-sm text-muted-foreground">No participants yet.</p>
                        @endforelse
                    </div>
                </x-card>
            </div>
        </div>

        <div class="w-full gap-2 border-muted-foreground/20 border-t mt-6 pt-6 flex justify-between items-center">
            <x-header
                title="Ideas"
                description="All the ideas for this team"
            />
            <x-card
                class="pointer my-10 text-left bg-primary btn flex items-center gap-2 ml-auto"
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
                <p>New idea</p>
            </x-card>
        </div>
        <div
            class="grid md:grid-cols-2 gap-6 mt-6"
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
    </div>
    <x-modals.team-modal
        name="edit-team"
        title="Edit Team"
        type="edit"
        :team="$team"
        :users="$users"
    />
    <x-modals.idea-modal
        name="create-idea"
        title="Create Idea"
        :fixedTeamId="$team->id"
    />
</x-layout.layout>
