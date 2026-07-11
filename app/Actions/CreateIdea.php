<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CreateIdea
{
    public function handle(array $attributes, ?User $user = null): void
    {
        /** @var User $user */
        $user ??= Auth::user();
        // i want aeverything from the validated request except the steps. tht belogns to a different table
        $data = collect($attributes)->only(['title', 'description', 'links', 'status', 'team_id'])->toArray();

        // handle the image
        if ($attributes['image'] ?? false) {
            $data['image_path'] = $attributes['image']->store('ideas', 'public');
        }

        if ($data['team_id']) {
            $teamExixsts = Team::where('id', $data['team_id'])->exists();
            if (! $teamExixsts) {
                throw ValidationException::withMessages([
                    'team_id' => 'The selected team is invalid.',
                ]);
            }

            $userInTeam = $user->teams()->where('teams.id', $data['team_id'])->exists();
            if (! $userInTeam) {
                throw ValidationException::withMessages([
                    'team_id' => 'The user is not a member of the specified team.',
                ]);
            }
        }

        // handle team assignment if not already there
        if (empty($data['team_id'])) {
            $personalTeam = $user->teams()
                ->where('teams.name', 'My Ideas')
                ->where('teams.private', true)
                ->wherePivot('role', 'owner')
                ->first();

            if (! $personalTeam) {
                $personalTeam = $user->teams()->create([
                    'name' => 'My Ideas',
                    'description' => 'Personal team for managing ideas',
                    'private' => true,
                ], [
                    'role' => 'owner',
                ]);
            }

            $data['team_id'] = $personalTeam->id;
        }

        // create many steps the steps for the idea (steps have their own table and relationship with the idea)
        $steps = collect($attributes['steps'] ?? [])->map(fn ($step) => ['description' => $step['description'] ?? $step, 'completed' => $step['completed'] ?? false]);

        // ? i create a transaction to make sure that if any of the steps fail, the idea creation will be rolled back and not saved in the database. this is important because if the idea is created but the steps fail, we will have an incomplete idea in the database.

        DB::transaction(function () use ($user, $data, $steps) {
            // create the idea for the user
            $idea = $user->ideas()->create($data);
            $idea->steps()->createMany($steps);
        });

    }
}
