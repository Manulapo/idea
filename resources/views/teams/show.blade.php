<x-layout.layout>
    <div class="py-8 max-w-4xl mx-auto">
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
    </div>
    <x-modals.team-modal
        name="edit-team"
        title="Edit Team"
        type="edit"
        :team="$team"
        :users="$users"
    />
</x-layout.layout>
