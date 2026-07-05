<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\Idea;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UpdateIdea
{
    public function handle(array $attributes, Idea $idea): void
    {
        // i want everything from the validated request except the steps. tht belogns to a different table
        $data = collect($attributes)->only(['title', 'description', 'links', 'status'])->toArray();

        // handle the image
        if ($attributes['image'] ?? false) {
            $data['image_path'] = $attributes['image']->store('ideas', 'public');
        }

        if ($attributes['remove_image'] ?? false) {
            Storage::delete($idea->image_path);
            $idea->image_path = null;
        }

        // create many steps the steps for the idea (steps have their own table and relationship with the idea)

        DB::transaction(function () use ($idea, $data, $attributes) {
            // update the idea
            $idea->update($data);

            // Map steps data and ensure we only keep description and completed (strip old IDs)
            $steps = collect($attributes['steps'] ?? [])
                ->map(fn ($step) => [
                    'description' => $step['description'] ?? $step,
                    'completed' => ($step['completed'] ?? false) === 'true' || ($step['completed'] ?? false) === true,
                ]);

            $idea->steps()->delete(); // delete existing steps before creating new ones

            $idea->steps()->createMany($steps->toArray()); // rebuild the steps with the new data

            // ? we could have used the upsert method to update existing steps and create new ones, but since we are replacing all steps, it's simpler to delete and recreate them. This ensures that any removed steps are not lingering in the database.
        });

        // Refresh the idea to load the new steps
        $idea->load('steps');
    }
}
