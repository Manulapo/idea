<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Step;
use Illuminate\Support\Facades\Gate;

class StepController extends Controller
{
    public function update(Step $step)
    {
        Gate::authorize('editIdea', $step->idea);

        $step->update([
            'description' => $step->description,
            'completed' => ! $step->completed,
        ]);

        return back();
    }
}
