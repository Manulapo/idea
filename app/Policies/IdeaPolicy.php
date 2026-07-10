<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Idea;
use App\Models\User;

class IdeaPolicy
{
    /**
     * Determine whether the user can work with the model.
     */
    public function workWith(User $user, Idea $idea): bool
    {
        $isUserOwner = $idea->user->is($user);
        $isUserInIdeaTeam = $user->teams()->whereKey($idea->team_id)->exists();

        return $isUserOwner || $isUserInIdeaTeam;
    }

    public function editIdea(User $user, Idea $idea): bool
    {
        $isUserOwner = $idea->user->is($user);

        return $isUserOwner;
    }
}
