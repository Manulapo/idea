<?php

use App\Models\User;

it('logs in a user', function () {
    // create a user in the database
    User::factory()->create([
        'name' => 'John doe',
        'email' => 'johndoe@something.com',
        'password' => '123456789',
    ]);

    // simulate a user visiting the login page, filling out the form, and submitting it
    visit('/login')
        ->fill('email', 'johndoe@something.com')
        ->fill('password', '123456789')
        ->click('@login')
        ->assertPathIs('/ideas');

    // assert that the user is authenticated
    $this->assertAuthenticated();

    // check if the user is in the database
    $this->assertDatabaseHas('users', [
        'name' => 'John doe',
        'email' => 'johndoe@something.com',
    ]);
});

it('logs out a user', function () {
    // create a user in the database
    $user = User::factory()->create();

    $this->actingAs($user);

    visit('/')
        ->click('@profile-menu-trigger')
        ->click('@logout')
        ->assertPathIs('/login');

    // assert that the user is no longer authenticated
    $this->assertGuest();
});

it('requires a valid email address to log in', function () {
    visit('/login')
        ->fill('email', 'invalid-email')
        ->fill('password', '123456789')
        ->click('@login')
        ->assertPathIs('/login');
});
