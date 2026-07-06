@props(['team'])

@php
    $isTeamAdmin = $team
        ->users()
        ->where('user_id', auth()->id())
        ->wherePivot('role', 'admin')
        ->exists();

@endphp

<a
    href="{{ route('teams.show', $team) }}"
    class="rounded-full bg-white/10 w-fit px-4 py-2 text-foreground/80 text-xs flex items-center gap-1"
>
    <iconify-icon
        icon="lucide:users"
        class="mr-1"
        width="16"
        height="16"
    ></iconify-icon>
    {{ $team->name }}
    @if ($isTeamAdmin)
        <span
            class="bg-primary text-black px-2 py-1 rounded-full text-[10px] ml-2"
            aria-label="You are an admin of this team"
        >Admin</span>
    @endif
</a>
