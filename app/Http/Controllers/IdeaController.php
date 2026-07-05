<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateIdea;
use App\Actions\UpdateIdea;
use App\Http\Requests\FilterIdeasRequest;
use App\Http\Requests\StoreIdeaRequest;
use App\IdeaStatus;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class IdeaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(FilterIdeasRequest $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $ideas = $user
            ->ideas()
            ->when(in_array($request->validated('status'), IdeaStatus::values()), fn ($query) => $query
                ->where('status', $request->validated('status')))
            ->latest()
            ->get();

        return view('ideas.index', [
            'ideas' => $ideas,
            'statusCounts' => Idea::statusCounts(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIdeaRequest $request, CreateIdea $createIdea)
    {
        $createIdea->handle($request->validated());

        return redirect()->route('ideas.index')->with('success', 'Idea created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Idea $idea)
    {
        Gate::authorize('workWith', $idea);

        $idea->load('steps');

        return view('ideas.show', [
            'idea' => $idea,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Idea $idea): void
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreIdeaRequest $request, Idea $idea, UpdateIdea $updateIdea)
    {
        Gate::authorize('workWith', $idea);

        $attributes = $request->validated();

        $updateIdea->handle($attributes, $idea);

        return redirect()->route('ideas.show', $idea)->with('success', 'Idea updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idea $idea)
    {
        // the gate facade autorize method work s well with the policy file. In that IdeaPolicy.php i set the workWith method to check if the user is the owner of the idea. If not, it will throw an exception and return a 403 error.
        Gate::authorize('workWith', $idea);
        $idea->delete();

        // we can also use the can method directly into the route or the blade file to check if the user can work with the idea. If not, it will return a 403 error.
        // Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy'])->can('workWith', 'idea');

        return redirect()->route('ideas.index');
    }
}
