<?php

it('registers a user', function () {
    // simulate a user visiting the registration page, filling out the form, and submitting it
    visit('/register')
        ->fill('name', 'John doe')
        ->fill('email', 'johndoe@something.com')
        ->fill('password', '123456789')
        ->fill('password_confirmation', '123456789')
        ->click('Create account')
        ->assertPathIs('/ideas');

    // assert that the user is authenticated
    $this->assertAuthenticated();

    // check if the user is in the database
    $this->assertDatabaseHas('users', [
        'name' => 'John doe',
        'email' => 'johndoe@something.com',
    ]);
});

it('requires a valid email address to sign up', function () {
    visit('/register')
        ->fill('name', 'John doe')
        ->fill('email', 'invalid-email')
        ->fill('password', '123456789')
        ->fill('password_confirmation', '123456789')
        ->click('Create account')
        ->assertPathIs('/register');
});
