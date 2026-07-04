<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\CreateIdea;
use App\Http\Requests\FilterIdeasRequest;
use App\Http\Requests\StoreIdeaRequest;
use App\Http\Requests\UpdateIdeaRequest;
use App\IdeaStatus;
use App\Models\Idea;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

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
    public function update(UpdateIdeaRequest $request, Idea $idea): void
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Idea $idea)
    {
        $idea->delete();

        return redirect()->route('ideas.index');
    }
}
