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
