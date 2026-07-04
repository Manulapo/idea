<x-layout.layout>
    <div class="py-8 max-w-4xl mx-auto">
        <div class="flex justify-between items-center gap-2">

            <a href="{{ route('ideas.index') }}" class="flex items-center gap-2">
                <x-icons.arrow-back class="text-foreground" />
                Back to Ideas</a>

            <div class="gap-x-3 flex items-center">
                <button class="btn btn-outlined flex items-center"> <x-icons.external class="text-foreground size-1" />
                    Edit Idea</button>
                <x-form.form action="{{ route('ideas.destroy', $idea->id) }}" method="DELETE" class="inline">
                    <button type="submit" class="btn btn-outlined text-red-400">Delete</button>
                </x-form.form>
            </div>
        </div>

        <div class="py-8 space-y-6">
            @if ($idea->image_path)
                <div class="rounded-lg overflow-hidden">
                    <img src="{{ asset('storage/' . $idea->image_path) }}" alt="{{ $idea->title }}"
                        class="w-full h-90 object-cover">
                </div>
            @endif
            <h1 class="text-foreground text-3xl font-bold mb-6">{{ $idea->title }}</h1>
            <div class="mt-2 flex gap-x-3 items-center">
                <x-status-label :status="$idea->status">
                    {{ $idea->status->label() }}
                </x-status-label>

                <div class="flex items-center gap-x-2 text-sm text-muted-foreground">
                    <span>{{ $idea->created_at->diffForHumans() }}</span>
                </div>
            </div>
            <x-card href="{{ '/ideas/' . $idea->id }}" flex="flex flex-col gap-2">
                <p class="text-foreground max-w-none cursor-pointer">{{ $idea->description }}</p>
            </x-card>

            @if ($idea->steps && count($idea->steps) > 0)
                <div>
                    <h3 class="text-foreground text-xl font-bold mb-4">Steps</h3>

                    <div>
                        @foreach ($idea->steps as $step)
                            <x-form.form method="PATCH" action="{{ route('steps.update', $step->id) }}"
                                class="mb-2 w-full max-w-full">
                                <x-card target="_blank" class="flex items-center gap-2 mb-2">
                                    <button type="submit" aria-role="checkbox"
                                        class="size-5 flex items-center justify-center rounded-lg text-card  border {{ $step->completed ? 'bg-primary' : 'border border-primary' }}">&check;</button>
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
                            <x-card href="{{ $link }}" target="_blank"
                                class="flex items-center gap-2 space-y-2 mb-2">
                                <x-icons.external class="fill-primary" :size="18" />
                                <p class="text-primary max-w-none cursor-pointer">{{ $link }}</p>
                            </x-card>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-layout.layout>
