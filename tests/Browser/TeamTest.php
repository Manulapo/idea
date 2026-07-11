<?php

use App\Models\Team;
use App\Models\User;

it('lists teams the user belongs to', function () {
    $user = User::factory()->create();
    $teamA = Team::factory()->create(['name' => 'Team Alpha']);
    $teamA->users()->attach($user, ['role' => 'member']);

    $teamB = Team::factory()->create(['name' => 'Team Beta']);

    $this->actingAs($user);

    visit('/teams')
        ->assertSee('Team Alpha')
        ->assertDontSee('Team Beta');
});

it('creates a new team', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create(['name' => 'Jane Doe']);

    $this->actingAs($user);

    visit('/teams')
        ->click('@create-idea-button')
        ->fill('name', 'My New Team')
        ->fill('description', 'This is my new team description')
        ->click('Select participants for your team')
        ->click('Jane Doe')
        ->click('Team Name')
        ->click('@create-team-submit-button')
        ->assertPathIs('/teams');

    $this->assertDatabaseHas('teams', [
        'name' => 'My New Team',
        'description' => 'This is my new team description',
    ]);

    $team = Team::where('name', 'My New Team')->first();
    expect($team->users->pluck('id')->all())->toContain($user->id, $otherUser->id);
});

it('displays team details to its members', function () {
    $user = User::factory()->create(['name' => 'Owner User']);
    $otherUser = User::factory()->create(['name' => 'Member User']);

    $team = Team::factory()->create([
        'name' => 'Awesome Team',
        'description' => 'Awesome description',
    ]);

    // Attach owner
    $team->users()->attach($user, ['role' => 'owner']);
    // Attach member
    $team->users()->attach($otherUser, ['role' => 'member']);

    // Create an idea for the team
    $idea = \App\Models\Idea::factory()->create([
        'title' => 'Team Idea 1',
        'team_id' => $team->id,
        'user_id' => $user->id,
    ]);

    $this->actingAs($user);

    visit('/teams/' . $team->id)
        ->assertSee('Awesome Team')
        ->assertSee('Awesome description')
        ->assertSee('Owner User')
        ->assertSee('Member User')
        ->assertSee('Team Idea 1');
});

it('allows owners/admins to edit the team', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create([
        'name' => 'Original Team Name',
        'description' => 'Original Team Description',
    ]);
    $team->users()->attach($user, ['role' => 'owner']);

    // Create another user to select as participant
    $otherUser = User::factory()->create(['name' => 'Bob Smith']);

    $this->actingAs($user);

    visit('/teams/' . $team->id)
        ->click('Edit Team')
        ->fill('name', 'Updated Team Name')
        ->fill('description', 'Updated Team Description')
        ->click('@edit-team-submit-button')
        ->assertPathIs('/teams/' . $team->id)
        ->assertSee('Updated Team Name')
        ->assertSee('Updated Team Description');

    $this->assertDatabaseHas('teams', [
        'id' => $team->id,
        'name' => 'Updated Team Name',
        'description' => 'Updated Team Description',
    ]);
});

it('allows owners/admins to change member roles and remove members', function () {
    $owner = User::factory()->create();
    $member = User::factory()->create(['name' => 'John Member']);

    $team = Team::factory()->create();
    $team->users()->attach($owner, ['role' => 'owner']);
    $team->users()->attach($member, ['role' => 'member']);

    $this->actingAs($owner);

    visit('/teams/' . $team->id)
        ->assertSee('John Member')
        ->click('[aria-label="You are an admin of this team"]') // Promote to Admin
        ->assertPathIs('/teams/' . $team->id);

    // Verify role updated to admin in db
    $team->refresh();
    expect($team->users()->where('user_id', $member->id)->first()->pivot->role)->toBe('admin');

    // Demote back to member so we can remove them
    visit('/teams/' . $team->id)
        ->click('[aria-label="You are a member of this team"]')
        ->assertPathIs('/teams/' . $team->id);

    $team->refresh();
    expect($team->users()->where('user_id', $member->id)->first()->pivot->role)->toBe('member');

    // Remove user from team
    visit('/teams/' . $team->id)
        ->click('[aria-label="Remove user from team"]')
        ->assertPathIs('/teams/' . $team->id);

    // Verify user is removed
    $team->refresh();
    expect($team->users()->where('user_id', $member->id)->exists())->toBeFalse();
});

it('allows owners to delete a team', function () {
    $user = User::factory()->create();
    $team = Team::factory()->create(['name' => 'Team to Delete']);
    $team->users()->attach($user, ['role' => 'owner']);

    $this->actingAs($user);

    visit('/teams/' . $team->id)
        ->click('Delete')
        ->assertPathIs('/teams');

    $this->assertDatabaseMissing('teams', [
        'id' => $team->id,
    ]);
});
