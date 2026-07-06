<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTeamRequest;
use App\Http\Requests\UpdateTeamRequest;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $user = Auth::user();

        return view('teams.index', [
            'teams' => $user->teams,
            'users' => User::all(),
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
    public function store(StoreTeamRequest $request)
    {
        $data = $request->validated();
        $team = Team::create([
            'name' => $data['name'],
        ]);

        // attach the participants to the team
        $participants = $data['participants'] ?? [];
        $user = Auth::user();
        foreach ($participants as $participantId) {
            $role = ((int) $user->id == (int) $participantId) ? 'admin' : 'member';
            $team->users()->syncWithPivotValues($participantId, ['role' => $role], false);
        }

        return redirect()->route('teams.index')->with('success', 'Team created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Team $team)
    {
        return view('teams.show', [
            'team' => $team,
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
    public function update(UpdateTeamRequest $request, Team $team)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $user = Auth::user();
        // check for tem existance and if the role of the user is admin
        if ($team->users()
            ->where('user_id', $user->id)
            ->wherePivot('role', 'admin')
            ->exists()) {
            $team->users()->detach(); // Detach all users from the team
            $team->delete(); // Delete the team

            return redirect()->route('teams.index')->with('success', 'Team deleted successfully.');
        }

        return redirect()->route('teams.index')->with('error', 'You are not authorized to delete this team.');
    }
}
