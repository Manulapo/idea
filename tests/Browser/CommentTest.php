<?php

use App\Models\Comment;
use App\Models\Idea;
use App\Models\User;

it('lists comments on the idea detail page', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create(['user_id' => $user->id]);

    Comment::factory()->create([
        'idea_id' => $idea->id,
        'user_id' => $user->id,
        'content' => 'This is comment 1',
    ]);

    Comment::factory()->create([
        'idea_id' => $idea->id,
        'user_id' => $user->id,
        'content' => 'This is comment 2',
    ]);

    $this->actingAs($user);

    visit('/ideas/'.$idea->id)
        ->assertSee('This is comment 1')
        ->assertSee('This is comment 2');
});

it('allows an authenticated user to add a comment', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user);

    visit('/ideas/'.$idea->id)
        ->fill('content', 'This is a new comment')
        ->click('Add Comment')
        ->assertPathIs('/ideas/'.$idea->id)
        ->assertSee('This is a new comment');

    $this->assertDatabaseHas('comments', [
        'idea_id' => $idea->id,
        'user_id' => $user->id,
        'content' => 'This is a new comment',
    ]);
});

it('allows the owner to edit their comment', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create(['user_id' => $user->id]);
    $comment = Comment::factory()->create([
        'idea_id' => $idea->id,
        'user_id' => $user->id,
        'content' => 'Original Comment Text',
    ]);

    $this->actingAs($user);

    visit('/ideas/'.$idea->id)
        ->assertSee('Original Comment Text')
        ->click('[aria-label="Comment options"]')
        ->click('Edit')
        ->fill('form[action$="/comments/'.$comment->id.'"] input[name="content"]', 'Updated Comment Text')
        ->click('form[action$="/comments/'.$comment->id.'"] button.btn-primary')
        ->assertPathIs('/ideas/'.$idea->id)
        ->assertSee('Updated Comment Text')
        ->assertDontSee('Original Comment Text');

    $this->assertDatabaseHas('comments', [
        'id' => $comment->id,
        'content' => 'Updated Comment Text',
    ]);
});

it('allows the owner to delete their comment', function () {
    $user = User::factory()->create();
    $idea = Idea::factory()->create(['user_id' => $user->id]);
    $comment = Comment::factory()->create([
        'idea_id' => $idea->id,
        'user_id' => $user->id,
        'content' => 'Comment to delete',
    ]);

    $this->actingAs($user);

    visit('/ideas/'.$idea->id)
        ->assertSee('Comment to delete')
        ->click('[aria-label="Comment options"]')
        ->click('form[action$="/comments/'.$comment->id.'"] button.text-red-500') // Click delete submit button
        ->assertPathIs('/ideas/'.$idea->id)
        ->assertDontSee('Comment to delete');

    $this->assertDatabaseMissing('comments', [
        'id' => $comment->id,
    ]);
});
