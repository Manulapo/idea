<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeamRequest;
use App\Models\Team;
use App\Models\User;
use App\TeamRole;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        $members = $user->teams()->withCount('users')->get();

        return view('teams.index', [
            'teams' => $user->teams,
            'currentUser' => $user,
            'users' => User::all(),
            'member_count' => $members,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TeamRequest $request)
    {
        $data = $request->validated();
        $team = Team::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        // attach the participants to the team
        $user = Auth::user();

        $participantIds = collect($data['participants'] ?? [])
            ->map(fn ($id) => (int) $id) // convert to int to avoid any issues with string ids
            ->push((int) $user->id) // ensure the creator is always a participant
            ->unique() // remove duplicates
            ->values(); // reindex the collection

        $syncData = $participantIds
            ->mapWithKeys(fn (int $participantId) => [
                $participantId => [
                    'role' => $participantId === (int) $user->id
                        ? TeamRole::OWNER->value
                        : TeamRole::MEMBER->value,
                ],
            ])
            ->all();

        $team->users()->sync($syncData); // sync is a method that will add new participants and remove those not in the list, but will not remove owners

        return redirect()->route('teams.index')->with('success', 'Team created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        $currentUser = Auth::user();

        // team user (eag er load the pivot table to get the role of the user in the team)
        $team->load('users');
        $currentRoleValue = $team->users
            ->firstWhere('id', $currentUser->id)?->pivot?->role;

        return view('teams.show', [
            'team' => $team,
            'users' => User::all(),
            'currentUserRole' => $currentRoleValue ? TeamRole::from($currentRoleValue) : TeamRole::MEMBER,
            'admins' => $team->users()->wherePivotIn('role', [TeamRole::ADMIN->value, TeamRole::OWNER->value])->get(),
            'members' => $team->users()->wherePivot('role', TeamRole::MEMBER->value)->get(),
            'ideas' => $team->ideas()->latest()->get(),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TeamRequest $request, Team $team)
    {
        $data = $request->validated();
        $team->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        // handle participants update
        $participantIds = collect($data['participants'] ?? [])
            ->map(fn ($id) => (int) $id);

        // get the owner ids to ensure they are not removed from the team
        $ownerIds = $team->users()
            ->wherePivot('role', TeamRole::OWNER->value)
            ->pluck('users.id');

        // merge the owner ids with the participant ids to ensure they are not removed from the team
        $participantIds = $participantIds
            ->merge($ownerIds)
            ->unique()
            ->values();

        $existingRoles = $team->users()
            ->pluck('team_user.role', 'users.id');

        $syncData = $participantIds
            ->mapWithKeys(fn (int $participantId) => [
                $participantId => [
                    'role' => $existingRoles[$participantId] ?? TeamRole::MEMBER->value,
                ],
            ])
            ->all();

        // sync the participants with the team, this will add new participants and remove those not in the list, but will not remove owners
        $team->users()->sync($syncData);

        return redirect()->route('teams.show', $team)->with('success', 'Team updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $user = Auth::user();
        // check for team existence and if the role of the user is admin
        if ($team->users()
            ->where('user_id', $user->id)
            ->wherePivotIn('role', [TeamRole::ADMIN->value, TeamRole::OWNER->value])
            ->exists()) {
            $team->users()->detach(); // Detach all users from the team
            $team->delete(); // Delete the team

            return redirect()->route('teams.index')->with('success', 'Team deleted successfully.');
        }

        return redirect()->route('teams.index')->with('error', 'You are not authorized to delete this team.');
    }

    /**
     * Change the role of a user in a team.
     */
    public function changeRole(Team $team, User $user)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $currentTeam = $team->users()
            ->where('user_id', $currentUser->id)
            ->firstOrFail();

        $targetTeam = $team->users()
            ->where('user_id', $user->id)
            ->firstOrFail();

        $currentUserRole = TeamRole::from($currentTeam->pivot->role);
        $targetUserRole = TeamRole::from($targetTeam->pivot->role);

        if (! $currentUserRole->canManageUsers()) {
            return redirect()
                ->route('teams.show', $team)
                ->with('error', 'You are not authorized to change this user\'s role.');
        }

        if ($targetUserRole === TeamRole::OWNER) {
            return redirect()
                ->route('teams.show', $team)
                ->with('error', 'The owner role cannot be changed.');
        }

        $newRole = $targetUserRole === TeamRole::ADMIN
            ? TeamRole::MEMBER
            : TeamRole::ADMIN;

        $team->users()->updateExistingPivot($user->id, ['role' => $newRole->value]);

        return redirect()
            ->route('teams.show', $team)
            ->with('success', 'User role updated successfully.');
    }

    public function removeUserFromTeam(Team $team, User $user)
    {
        /** @var User $currentUser */
        $currentUser = Auth::user();

        $currentMembership = $team->users()
            ->where('user_id', $currentUser->id)
            ->firstOrFail();

        $targetMembership = $team->users()
            ->where('user_id', $user->id)
            ->firstOrFail();

        $currentUserRole = TeamRole::from($currentMembership->pivot->role);
        $targetUserRole = TeamRole::from($targetMembership->pivot->role);

        if (! $currentUserRole->canManageUsers()) {
            return redirect()
                ->route('teams.show', $team)
                ->with('error', 'You are not authorized to remove users from this team.');
        }

        if ($targetUserRole === TeamRole::OWNER) {
            return redirect()
                ->route('teams.show', $team)
                ->with('error', 'The owner cannot be removed from the team.');
        }

        $team->users()->detach($user->id);

        return redirect()
            ->route('teams.show', $team)
            ->with('success', 'User removed from the team successfully.');
    }
}
