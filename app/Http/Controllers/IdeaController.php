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
        $status = $request->validated('status');

        $teamIds = $user->teams()->pluck('teams.id');

        // list the valid status
        $dbStatuses = [
            IdeaStatus::PENDING->value,
            IdeaStatus::IN_PROGRESS->value,
            IdeaStatus::COMPLETED->value,
        ];

        $ideas = Idea::where(function ($query) use ($user, $teamIds) {
            $query->where('user_id', $user->id); // only include ideas created by the user

            if ($teamIds->isNotEmpty()) {
                $query->orWhereIn('team_id', $teamIds); // only include ideas from teams the user is a member of
            }
        })
            ->when(
                in_array($status, $dbStatuses, true),
                fn ($query) => $query->where('status', $status)
            )
            ->latest()
            ->get();

        return view('ideas.index', [
            'ideas' => $ideas,
            'statusCounts' => Idea::statusCounts(),
            'teams' => $user->teams,
            'user' => $user,
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

        $idIdeaOwner = $idea->user->id === Auth::id();
        $teams = Auth::user()->teams;

        // we use load to eager load the steps relationship to avoid the N+1 problem. This means that when we access the steps of the idea, it will not make a separate query for each step, but instead will load all the steps in one query.
        $idea->load('steps');

        return view('ideas.show', [
            'idea' => $idea,
            'isOwner' => $idIdeaOwner,
            'teams' => $teams,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Idea $idea): void
    {
        Gate::authorize('editIdea', $idea);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreIdeaRequest $request, Idea $idea, UpdateIdea $updateIdea)
    {
        Gate::authorize('editIdea', $idea);

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
        Gate::authorize('editIdea', $idea);
        $idea->delete();

        // we can also use the can method directly into the route or the blade file to check if the user can work with the idea. If not, it will return a 403 error.
        // Route::delete('/ideas/{idea}', [IdeaController::class, 'destroy'])->can('workWith', 'idea');

        return redirect()->route('ideas.index');
    }
}
