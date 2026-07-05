<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Idea;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

class IdeaImageController extends Controller
{
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idea $idea)
    {
        // authorize that the user can work with the idea (the user is the owner of the idea)
        Gate::authorize('workWith', $idea);

        // delete image from teh local storage
        Storage::disk('public')->delete($idea->image_path);

        // update the idea to remove the image path from the database
        $idea->update(['image_path' => null]);

        return back();
    }
}
