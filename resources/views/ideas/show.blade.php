<x-layout.layout>
    <x-header title="Ideas" description="All the ideas for this project" />

    <div class="mt-10">
        <div class="grid md:grid-cols-2 gap-6">
            <x-card href="{{ '/ideas/' . $idea->id }}" flex="flex flex-col gap-2">
                <h3 class="text-foreground text-lg">{{ $idea->title }}</h3>
                <x-status-label :status="$idea->status" />
                <p class="text-muted-foreground line-clamp-3 mt-5">{{ $idea->description }}</p>
                <div class="mt-4 text-sm text-muted-foreground/50">{{ $idea->created_at->diffForHumans() }}</div>
            </x-card>
        </div>
    </div>
</x-layout.layout>
