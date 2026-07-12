<?php

use App\Models\Idea;
use App\Models\User;

it('requires authentication', function () {
    Idea::factory()->create();

    visit('/ideas')
        ->assertPathIs('/login');

    visit('/ideas/create')
        ->assertPathIs('/login');

});

it('requires authorization to view an idea', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create();

    $this->actingAs($user);

    $this->get(route('ideas.show', $idea))
        ->assertForbidden();
});

it('shows an idea with a due date for an authorized user', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->for($user)->create([
        'due_date' => '2026-07-12 09:17:47',
    ]);

    $this->actingAs($user);

    $this->get(route('ideas.show', $idea))
        ->assertSuccessful()
        ->assertSee('July 12, 2026');
});
