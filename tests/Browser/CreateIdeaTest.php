<?php

use App\Models\Idea;
use App\Models\User;

it('creates a new idea', function () {
    $this->actingAs(User::factory()->create());

    visit('/ideas')
        ->click('@create-idea-button')
        ->fill('title', 'My first idea')
        ->click('@button-status-completed')
        ->fill('description', 'This is my first idea')
        ->fill('@new-link', 'https://example.com')
        ->click('@add-link-button')
        ->fill('@new-link', 'https://laravel.com')
        ->click('@add-link-button')
        ->fill('@new-step', 'Step 1')
        ->click('@add-step-button')
        ->fill('@new-step', 'Step 2')
        ->click('@add-step-button')
        ->click('@submit-idea-button')
        ->assertPathIs('/ideas');

    // todo fix
    expect(Idea::first()->toArray())->toMatchArray([
        'title' => 'My first idea',
        'description' => 'This is my first idea',
        'status' => 'completed',
        'links' => ['https://example.com', 'https://laravel.com'],
    ]);

});
