@php
    $assigneesOptions = $idea->team->users->map(
        fn($user) => [
            'id' => $user->id,
            'name' => $user->name,
            'avatar' => $user->image_path ? asset('storage/' . $user->image_path) : null,
        ],
    );
@endphp

<x-layout.layout>
    <div class="py-8 max-w-4xl mx-auto">
        <div class="flex justify-between items-center gap-2">

            <a
                href="{{ route('ideas.index') }}"
                class="flex items-center gap-2"
            >
                <iconify-icon
                    icon="lucide:arrow-left"
                    class="text-foreground"
                    width="24"
                    height="24"
                ></iconify-icon>
                Back to Ideas</a>
            @if ($isOwner)
                <div class="gap-x-3 flex items-center">
                    <button
                        x-data
                        class="btn btn-outlined flex items-center"
                        @click="$dispatch('open-modal', 'edit-idea')"
                    >
                        <iconify-icon
                            icon="lucide:external-link"
                            class="text-foreground mr-2"
                            width="16"
                            height="16"
                        ></iconify-icon>
                        Edit Idea</button>
                    <x-form.form
                        action="{{ route('ideas.destroy', $idea->id) }}"
                        method="DELETE"
                        class="inline"
                    >
                        <button
                            type="submit"
                            class="btn btn-outlined text-red-400"
                        >Delete</button>
                    </x-form.form>
                </div>
            @endif
        </div>

        <div class="py-8 space-y-8">
            @if ($idea->image_path)
                <div class="rounded-lg overflow-hidden relative group">
                    <img
                        src="{{ asset('storage/' . $idea->image_path) }}"
                        alt="{{ $idea->title }}"
                        class="w-full h-90 object-cover"
                    >
                    <button
                        form="delete-image-form"
                        class="btn flex items-center absolute right-4 bottom-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                        type="submit"
                    >
                        <iconify-icon
                            icon="lucide:trash-2"
                            class="mr-2"
                            width="20"
                            height="20"
                            aria-hidden="true"
                        ></iconify-icon>
                        Remove Image</button>
                </div>
            @endif

            <x-status-label :status="$idea->status">
                {{ $idea->status->label() }}
            </x-status-label>
            <h1 class="text-foreground text-3xl font-bold mb-6">{{ $idea->title }}</h1>
            <div class="mt-2 flex gap-x-3 items-center justify-end w-full">

                {{-- Assigned to --}}
                @if ($idea->team)
                    <x-form.form
                        action="{{ route('ideas.update-assignee', $idea->id) }}"
                        method="PATCH"
                        x-data
                        @select-picker-change.window="$el.submit()"
                        class="gap-2 max-w-0'"
                    >
                        <x-form.select-picker
                            name="assignee_id"
                            :options="$assigneesOptions"
                            :value="$idea->assignee ? $idea->assignee->id : null"
                            :display-label="$idea->assignee ? $idea->assignee->name : null"
                            placeholder="Assign to"
                            search-placeholder="Search members"
                            empty-message="No members found."
                            :multiple="false"
                            :with-avatars="true"
                            class="min-w-60"
                        />
                    </x-form.form>
                @endif

            </div>

            @if ($idea->description)
                <x-card
                    is="div"
                    href="{{ '/ideas/' . $idea->id }}"
                    flex="flex flex-col gap-2"
                >
                    <p class="text-foreground max-w-none cursor-pointer prose">{!! $idea->formattedDescription !!}</p>
                </x-card>
            @endif

            @if ($idea->steps && count($idea->steps) > 0)
                <div>
                    <h3 class="text-foreground text-xl font-bold mb-4">Steps</h3>

                    <div>
                        @foreach ($idea->steps as $step)
                            <x-form.form
                                method="PATCH"
                                action="{{ route('steps.update', $step->id) }}"
                                class="mb-2 w-full max-w-full"
                            >
                                <x-card
                                    target="_blank"
                                    class="flex items-center gap-2 mb-2"
                                >
                                    <button
                                        type="submit"
                                        aria-role="checkbox"
                                        class="size-5 flex items-center justify-center rounded-lg text-card  border {{ $step->completed ? 'bg-primary' : 'border border-primary' }}"
                                    >&check;</button>
                                    <p class="{{ $step->completed ? 'line-through text-muted-foreground' : '' }}">
                                        {{ $step->description }}
                                    </p>
                                </x-card>
                            </x-form.form>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($idea->links && count($idea->links) > 0)
                <div>
                    <h3 class="text-foreground text-xl font-bold mb-4">Links</h3>

                    <div>
                        @foreach ($idea->links as $link)
                            <x-card
                                href="{{ $link }}"
                                target="_blank"
                                class="flex items-center gap-2 space-y-2 mb-2"
                            >
                                <iconify-icon
                                    icon="lucide:external-link"
                                    class="text-primary"
                                    width="20"
                                    height="20"
                                ></iconify-icon>
                                <p class="text-primary max-w-none cursor-pointer">{{ $link }}</p>
                            </x-card>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="flex items-center gap-x-2 w-full justify-between">
                <div class="flex gap-1 items-center">
                    <x-user-avatar
                        :user="$idea->user"
                        size="6"
                    />
                    <span class="ml-1 text-sm text-muted-foreground">{{ $idea->user->name }}</span>
                    <span class="text-muted-foreground text-sm">in</span>
                    <span class="text-sm text-muted-foreground">
                        @if ($idea->team)
                            <a
                                href="{{ route('teams.show', $idea->team->id) }}"
                                class="text-sm text-primary/70 hover:underline"
                                onclick="event.stopPropagation()"
                            >
                                {{ $idea->team->name }}
                            </a>
                        @endif
                    </span>
                </div>
                <div class="flex gap-2 items-center">
                    <div class="flex items-center gap-x-2 text-sm text-muted-foreground">
                        <span>{{ $idea->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-foreground text-xl font-bold my-4">Comments</h3>

                <x-comments.index :comments="$idea->comments" />

                <x-comments.add :idea-id="$idea->id" />

            </div>
        </div>
    </div>

    <x-modals.idea-modal
        name="edit-idea"
        title="Edit Idea"
        type="edit"
        :idea="$idea"
        :teams="$teams"
    />

    @if ($idea->exists)
        <form
            id="delete-image-form"
            action="{{ route('ideas.delete-image', $idea->id) }}"
            method="POST"
            class="hidden"
        >
            @csrf
            @method('DELETE')
        </form>
    @endif
</x-layout.layout>
