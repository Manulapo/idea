@props(['user', 'viewerRole' => 'member', 'targetRole' => 'member', 'team' => new \App\Models\Team(), 'withOptions' => false])

@php
    use App\TeamRole;

    $canManageUsers = $viewerRole->canManageUsers();
    $isOwner = $targetRole === TeamRole::OWNER;
    $isAdmin = $targetRole === TeamRole::ADMIN;
    $isMember = $targetRole === TeamRole::MEMBER;
@endphp

<div
    {{ $attributes->merge(['class' => 'inline-flex items-center gap-3 rounded-full border border-border bg-white/5 px-2 py-1 text-sm text-foreground w-full']) }}>
    <x-user-avatar
        :user="$user"
        size="8"
        class="shrink-0"
    />

    <div class="min-w-0 pr-2">
        <p class="truncate font-medium">{{ $user->name }}</p>

        @if ($user->email)
            <p class="truncate text-xs text-muted-foreground">{{ $user->email }}</p>
        @endif
    </div>

    @if ($withOptions && $canManageUsers)
        <div class="ml-auto">
            <div class="w-full flex items-center justify-end gap-2 px-2">
                @if ($isOwner)
                    <span
                        class="bg-primary text-black px-2 py-1 rounded-md text-[10px]"
                        aria-label="You are the owner of this team"
                    >Owner</span>
                @else
                    <x-form.form
                        action="{{ route('teams.change-role', ['team' => $team, 'user' => $user]) }}"
                        method="PATCH"
                    >
                        <button type="submit">
                            <span
                                class="{{ $isAdmin ? 'bg-primary text-black' : 'bg-white/5' }} px-2 py-1 rounded-md text-[10px]"
                                aria-label="You are an admin of this team"
                            >Admin</span>
                        </button>
                    </x-form.form>
                    <x-form.form
                        action="{{ route('teams.change-role', ['team' => $team, 'user' => $user]) }}"
                        method="PATCH"
                    >
                        <button type="submit">
                            <span
                                class="{{ $isMember ? 'bg-primary text-black' : 'bg-white/5' }} px-2 py-1 rounded-md text-[10px]"
                                aria-label="You are a member of this team"
                            >Member</span>
                        </button>
                    </x-form.form>
                    @if ($canManageUsers && !$isAdmin)
                        <x-form.form
                            action="{{ route('teams.remove-user', ['team' => $team, 'user' => $user]) }}"
                            method="DELETE"
                        >
                            <button
                                type="submit"
                                class="text-red-400 flex items-center px-2 py-1 rounded-md"
                                aria-label="Remove user from team"
                            ><iconify-icon
                                    icon="mdi:delete"
                                    class="inline-block"
                                ></iconify-icon>
                            </button>
                        </x-form.form>
                    @endif
                @endif
            </div>
        </div>
    @endif
</div>
