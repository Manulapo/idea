<?php

use App\Models\Idea;
use App\Models\User;

it('belongs to a user', function () {
    // create an idea using the factory, which will automatically create a user and associate it with the idea
    $idea = Idea::factory()->create();

    // to be an instance of the User class, we can use the toBeInstanceOf() method provided by Pest, which checks if the given value is an instance of the specified class
    expect($idea->user)->toBeInstanceOf(User::class);
});

it('can have steps', function () {
    // create an idea using the factory, which will automatically create a user and associate it with the idea
    $idea = Idea::factory()->create();

    expect($idea->steps)->toBeEmpty();

    // create a step using the factory, which will automatically create an idea and associate it with the step
    $idea->steps()->create([
        'description' => 'This is a test step',
        'completed' => false,
    ]);

    // to be an instance of the Step class, we can use the toBeInstanceOf() method provided by Pest, which checks if the given value is an instance of the specified class
    expect($idea->fresh()->steps)
        ->toHaveCount(1);

    $step = $idea->fresh()->steps->first();
    expect($step->description)->toBe('This is a test step');
    expect($step->completed)->toBeFalse();

});
