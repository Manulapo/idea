<?php

use App\Actions\UpdateIdea;
use App\IdeaStatus;
use App\Models\Idea;
use App\Models\Step;
use App\Models\User;

it('updates an idea and replaces its steps', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()
        ->for($user)
        ->has(Step::factory()->count(2))
        ->create([
            'title' => 'Original Title',
            'description' => 'Original Description',
        ]);

    $this->actingAs($user);

    $originalStepIds = $idea->steps->pluck('id')->toArray();

    $updateIdea = new UpdateIdea;
    $updateIdea->handle([
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'status' => IdeaStatus::IN_PROGRESS,
        'links' => ['https://example.com'],
        'steps' => [
            ['description' => 'New Step 1', 'completed' => false],
            ['description' => 'New Step 2', 'completed' => true],
            ['description' => 'New Step 3', 'completed' => false],
        ],
    ], $idea);

    $idea->refresh();

    expect($idea->title)->toBe('Updated Title');
    expect($idea->description)->toBe('Updated Description');
    expect($idea->status)->toBe(IdeaStatus::IN_PROGRESS);

    expect($idea->links->toArray())->toBe(['https://example.com']);

    expect($idea->steps)->toHaveCount(3);
    expect($idea->steps->pluck('description')->toArray())->toBe([
        'New Step 1',
        'New Step 2',
        'New Step 3',
    ]);

    expect($idea->steps->pluck('completed')->toArray())->toBe([
        false,
        true,
        false,
    ]);

    // Verify old steps were deleted
    foreach ($originalStepIds as $oldId) {
        expect(Step::find($oldId))->toBeNull();
    }
});

it('handles removing all steps', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()
        ->for($user)
        ->has(Step::factory()->count(3))
        ->create();

    $this->actingAs($user);

    $updateIdea = new UpdateIdea;
    $updateIdea->handle([
        'title' => $idea->title,
        'description' => $idea->description,
        'status' => $idea->status,
        'links' => [],
        'steps' => [],
    ], $idea);

    $idea->refresh();

    expect($idea->steps)->toHaveCount(0);
});
