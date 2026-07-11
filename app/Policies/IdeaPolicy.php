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

    /**
     * Determine whether the user can edit the idea.
     */
    public function editIdea(User $user, Idea $idea): bool
    {
        $isUserOwner = $idea->user->is($user);

        return $isUserOwner;
    }

    /**
     * Determine whether the user can delete the idea.
     */
    public function deleteIdea(User $user, Idea $idea): bool
    {
        $isUserOwner = $idea->user->is($user);

        return $isUserOwner;
    }

    /**
     * Determine whether the user can edit the comment.
     */
    public function editComment(User $user, Idea $idea): bool
    {
        $comment = $idea->comments()->where('user_id', $user->id)->first();

        return $comment !== null;
    }

    /**
     * Determine whether the user can delete the comment.
     */
    public function deleteComment(User $user, Idea $idea): bool
    {
        $comment = $idea->comments()->where('user_id', $user->id)->first();

        return $comment !== null;
    }

    /**
     * Determine whether the user can edit the team associated with the idea.
     */
    public function editTeam(User $user, Idea $idea): bool
    {
        $isUserInTeam = $user->teams()->whereKey($idea->team_id)->exists();

        return $isUserInTeam;
    }

    /**
     * Determine whether the user can delete the team associated with the idea.
     */
    public function deleteTeam(User $user, Idea $idea): bool
    {
        $isUserInTeam = $user->teams()->whereKey($idea->team_id)->exists();

        return $isUserInTeam;
    }

    /**
     * Determine whether the role can be changed for the user in the team associated with the idea.
     */
    public function changeRole(User $user, Idea $idea): bool
    {
        $isUserInTeam = $user->teams()->whereKey($idea->team_id)->exists();
        $isUserAdmin = $user->teams()->whereKey($idea->team_id)->wherePivotIn('role', ['admin', 'owner'])->exists();

        return $isUserInTeam && $isUserAdmin;
    }
}
