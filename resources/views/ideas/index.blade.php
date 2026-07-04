<x-layout.layout>
    <x-header title="Ideas" description="All the ideas for this project" />

    <x-card href="{{ route('ideas.create') }}" class="h-32 pointer my-10 w-full text-left" is="button" type="button"
        x-data @click="$dispatch('open-modal', 'create-idea')" data-test="create-idea-button"
        aria-label="Create a new idea" aria-haspopup="dialog">
        <p>Whats the idea?</p>
    </x-card>

    <nav class="mt-4" aria-label="Filter ideas by status">
        <a href="/ideas" class="btn {{ request('status') === null ? 'btn-primary' : 'btn-outlined' }}"
            aria-current="{{ request('status') === null ? 'page' : 'false' }}">All</a>
        @foreach (App\IdeaStatus::cases() as $status)
            <a href="/ideas?status={{ $status->value }}"
                class="btn {{ request('status') === $status->value ? 'btn-primary' : 'btn-outlined' }}"
                aria-current="{{ request('status') === $status->value ? 'page' : 'false' }}">{{ $status->label() }}
                <span class="text-xs pl-3"
                    aria-label="{{ $statusCounts->get($status->value) }} ideas">{{ $statusCounts->get($status->value) }}</span></a>
        @endforeach
    </nav>

    <section class="mt-10" aria-labelledby="ideas-heading">
        <h2 id="ideas-heading" class="sr-only">
            @if (request('status'))
                {{ collect(App\IdeaStatus::cases())->firstWhere('value', request('status'))?->label() }} Ideas
            @else
                All Ideas
            @endif
        </h2>
        <div class="grid md:grid-cols-2 gap-6" role="list">
            @forelse ($ideas as $idea)
                <x-card href="{{ '/ideas/' . $idea->id }}" role="listitem"
                    aria-label="View idea: {{ $idea->title }}">
                    @if ($idea->image_path)
                        <div class="rounded-lg overflow-hidden -mx-4 -mt-4">
                            <img src="{{ asset('storage/' . $idea->image_path) }}" alt="{{ $idea->title }}"
                                class="w-full h-48 object-cover mb-4">
                        </div>
                    @endif
                    <h3 class="text-foreground text-lg mb-2">{{ $idea->title }}</h3>
                    <x-status-label :status="$idea->status" />
                    <p class="text-muted-foreground line-clamp-3 mt-5">{{ $idea->description }}</p>
                    <div class="mt-4 text-sm text-muted-foreground/50">
                        <time
                            datetime="{{ $idea->created_at->toIso8601String() }}">{{ $idea->created_at->diffForHumans() }}</time>
                    </div>
                </x-card>
            @empty
                <x-card role="status" aria-live="polite">
                    <p>No ideas yet</p>
                </x-card>
            @endforelse
        </div>
    </section>

    {{-- modal --}}
    <x-modals.modal name="create-idea" title="Create an idea">
        <x-form.form action="{{ route('ideas.store') }}" method="POST" aria-label="Create a new idea"
            @click.away="show = false" enctype="multipart/form-data">
            <div class="space-y-6" x-data="{ status: 'pending', newLink: '', links: [], newStep: '', steps: [] }">

                <x-form.form-field name="title" label="Title" type="text" placeholder="Enter a title for you idea"
                    required />

                <div class="space-y-2">
                    <label for="status" id="status-label">Status</label>

                    <div class="flex gap-x-3 mt-4" role="radiogroup" aria-labelledby="status-label">
                        @foreach (App\IdeaStatus::cases() as $statusOption)
                            <button type="button" data-test="button-status-{{ $statusOption->value }}" role="radio"
                                :aria-checked="status === @js($statusOption->value) ? 'true' : 'false'"
                                :class="status === @js($statusOption->value) ? 'btn btn-primary' : 'btn btn-outlined'"
                                aria-label="Set status to {{ $statusOption->label() }}"
                                @click="status = @js($statusOption->value)">{{ $statusOption->label() }}</button>
                        @endforeach
                    </div>

                    {{-- Hidden input to submit the status --}}
                    <input type="hidden" name="status" x-model="status">
                </div>
                <x-form.form-field name="description" label="Description" type="textarea"
                    placeholder="Describe your idea" />


                {{-- Images --}}
                <x-form.form-field name="image" label="Featured image" type="file" accept="image/*" multiple
                    placeholder="Upload image for your idea" />

                {{-- Steps --}}
                <div>
                    <fieldset class="space-y-3">
                        <legend class="text-sm font-medium text-foreground">Actionable Steps</legend>

                        {{-- Add new step input --}}
                        <div class="flex gap-x-2 items-center">
                            <input type="text" placeholder="Enter a Step for the idea" id="new-step"
                                autocomplete="off" class="input flex-1" spellcheck="false" x-model="newStep"
                                data-test="new-step" aria-label="New step"
                                @keydown.enter.prevent="if(newStep.trim()) { steps.push(newStep.trim()); newStep = ''; }" />
                            <button type="button" :disabled="newStep.trim().length === 0" class="btn btn-outlined h-10"
                                @click="steps.push(newStep.trim()); newStep = ''" data-test="add-step-button"
                                aria-label="Add step to list">
                                <x-icons.close class="rotate-45" aria-hidden="true" />
                                <span class="sr-only">Add step</span>
                            </button>
                        </div>

                        {{-- Display added steps --}}
                        <div role="list" aria-live="polite" aria-label="Added steps">
                            <template x-for="(step, index) in steps" :key="index">
                                <div class="flex gap-x-2 items-center border border-border rounded-lg p-3 my-2"
                                    role="listitem">
                                    <input type="hidden" name="steps[]" :value="step" />
                                    <span class="flex-1 text-sm truncate" x-text="step"
                                        :aria-label="'Step: ' + step"></span>
                                    <button type="button" @click="steps.splice(index, 1)"
                                        data-test="remove-step-button" :aria-label="'Remove step ' + step">
                                        <x-icons.close aria-hidden="true" />
                                        <span class="sr-only">Remove step</span>
                                    </button>
                                </div>
                            </template>
                        </div>

                    </fieldset>
                </div>
                {{-- Links --}}
                <div>
                    <fieldset class="space-y-3">
                        <legend class="text-sm font-medium text-foreground">Links</legend>

                        {{-- Add new link input --}}
                        <div class="flex gap-x-2 items-center">
                            <input type="url" placeholder="Enter a URL" id="new-link" autocomplete="url"
                                class="input flex-1" spellcheck="false" x-model="newLink" data-test="new-link"
                                aria-label="New link URL"
                                @keydown.enter.prevent="if(newLink.trim()) { links.push(newLink.trim()); newLink = ''; }" />
                            <button type="button" :disabled="newLink.trim().length === 0"
                                class="btn btn-outlined h-10" @click="links.push(newLink.trim()); newLink = ''"
                                data-test="add-link-button" aria-label="Add link to list">
                                <x-icons.close class="rotate-45" aria-hidden="true" />
                                <span class="sr-only">Add link</span>
                            </button>
                        </div>

                        {{-- Display added links --}}
                        <div role="list" aria-live="polite" aria-label="Added links">
                            <template x-for="(link, index) in links" :key="index">
                                <div class="flex gap-x-2 items-center border border-border rounded-lg p-3 my-2"
                                    role="listitem">
                                    <input type="hidden" name="links[]" :value="link" />
                                    <span class="flex-1 text-sm truncate" x-text="link"
                                        :aria-label="'Link: ' + link"></span>
                                    <button type="button" @click="links.splice(index, 1)"
                                        data-test="remove-link-button" :aria-label="'Remove link ' + link">
                                        <x-icons.close aria-hidden="true" />
                                        <span class="sr-only">Remove link</span>
                                    </button>
                                </div>
                            </template>
                        </div>

                    </fieldset>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" class="btn btn-outlined h-10" @click="$dispatch('close-modal')"
                        aria-label="Cancel and close dialog">Cancel</button>
                    <button type="submit" class="btn h-10" data-test="create">Create idea</button>
                </div>
            </div>
        </x-form.form>
    </x-modals.modal>

</x-layout.layout>
