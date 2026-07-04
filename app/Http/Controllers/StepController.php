<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Step;

class StepController extends Controller
{
    public function update(Step $step)
    {
        $step->update([
            'description' => $step->description,
            'completed' => ! $step->completed,
        ]);

        return back();
    }
}
