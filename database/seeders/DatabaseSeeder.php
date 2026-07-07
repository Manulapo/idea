<?php

namespace Database\Seeders;

use App\Models\Idea;
use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $firstUser = User::factory()->create([
            'name' => 'Manuel',
            'email' => 'test@test.com',
            'password' => '12345678', // password
        ]);

        // create another user
        $secondUser = User::factory()->create([
            'name' => 'User 2',
            'email' => 'test2@example.com',
            'password' => '12345678', // password
        ]);

        User::factory(3)->create(); // create 3 more users

        // create 5 ideas for the test user
        Idea::factory(5)->create(['user_id' => $firstUser->id]);

        // create 5 ideas for the second user
        Idea::factory(5)->create(['user_id' => $secondUser->id]);

        // create 5 comments for the first idea
        Idea::find(1)->comments()->createMany([
            [
                'user_id' => 1,
                'content' => 'This is a comment for the first idea by the first user.',
            ],
            [
                'user_id' => 2,
                'content' => 'This is a comment for the first idea by the second user.',
            ],
            [
                'user_id' => 1,
                'content' => 'This is another comment for the first idea by the first user.',
            ],
            [
                'user_id' => 2,
                'content' => 'This is another comment for the first idea by the second user.',
            ],
            [
                'user_id' => 1,
                'content' => 'This is yet another comment for the first idea by the first user.',
            ],
        ]);

        // create a team for the first user
        $teams = Team::factory(3)->create();

        // attach users to every team using explicit roles on the pivot table
        $teams->each(function (Team $team) use ($firstUser, $secondUser): void {
            $team->users()->attach($firstUser->id, ['role' => 'admin']);
            $team->users()->attach($secondUser->id, ['role' => 'member']);
        });
    }
}
