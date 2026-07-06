<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('adds a profile image', function () {
    $user = User::factory()->create([
        'image_path' => null,
    ]);

    $this->actingAs($user);
    Storage::fake('public');

    $response = $this->patch('/profile', [
        'name' => $user->name,
        'email' => $user->email,
        'image_path' => UploadedFile::fake()->image('profile.jpg'),
    ]);

    $response
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success', 'Profile updated successfully.');

    $user->refresh();

    expect($user->image_path)
        ->not->toBeNull()
        ->toStartWith('profile_images/');

    expect(Storage::disk('public')->exists($user->image_path))->toBeTrue();
});

it('updates an existing profile image', function () {
    $user = User::factory()->create([
        'image_path' => 'profile_images/old-avatar.svg',
    ]);

    $this->actingAs($user);
    Storage::fake('public');

    $response = $this->patch('/profile', [
        'name' => $user->name,
        'email' => $user->email,
        'image_path' => UploadedFile::fake()->image('new-avatar.jpg'),
    ]);

    $response
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success', 'Profile updated successfully.');

    $updatedUser = $user->fresh();

    expect($updatedUser)
        ->not->toBeNull();

    expect($updatedUser->image_path)
        ->not->toBe('profile_images/old-avatar.svg')
        ->toStartWith('profile_images/');

    expect(Storage::disk('public')->exists($updatedUser->image_path))->toBeTrue();
});

it('removes the current profile image', function () {
    $user = User::factory()->create([
        'image_path' => 'profile_images/current-avatar.svg',
    ]);

    $this->actingAs($user);

    $response = $this->delete('/profile/image/delete');

    $response
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success', 'Profile image deleted successfully.');

    $user->refresh();

    expect($user->image_path)->toBeNull();
});

it('updates the profile info', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->patch('/profile', [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ]);

    $response
        ->assertRedirect(route('profile.edit'))
        ->assertSessionHas('success', 'Profile updated successfully.');

    $user->refresh();

    expect($user->name)->toBe('Updated Name');
    expect($user->email)->toBe('updated@example.com');
});
