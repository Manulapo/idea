@props([
    'name' => 'create-idea',
    'title' => 'Create an idea',
    'type' => 'create',
    'idea' => new App\Models\Idea(),
    'teams' => [],
    'fixedTeamId' => null,
])

@php
    $formAction = $type === 'create' ? route('ideas.store') : route('ideas.update', $idea->id);
    $formMethod = $type === 'create' ? 'POST' : 'PATCH';
    $resolvedTeamId = $fixedTeamId ? (string) $fixedTeamId : (string) old('team_id', $idea->team_id);
    $shouldHideTeamField = filled($fixedTeamId);
@endphp

<x-modals.modal
    name="{{ $name }}"
    title="{{ $title }}"
    data-test="{{ $name }}-modal"
>
    <x-form.form
        action="{{ $formAction }}"
        method="{{ $formMethod }}"
        aria-label="{{ $type === 'create' ? 'Create a new idea' : 'Edit idea' }}"
        :enctype="app()->environment('testing') ? null : 'multipart/form-data'"
    >
        <div
            class="space-y-6"
            x-data="{
                status: '{{ old('status', $idea->status) }}',
                newLink: '',
                links: @js(old('links', $idea->exists ? $idea->links : [])),
                newStep: '',
                steps: @js(old('steps', $idea->exists ? $idea->steps->map->only('id', 'description', 'completed') : [])),
                removeImage: false,
                teamId: '{{ old('team_id', $idea->team_id) }}',
            }"
        >

            <x-form.form-field
                name="title"
                label="Title"
                type="text"
                placeholder="Enter a title for you idea"
                :value="$idea->title"
                required
            />

            @if ($shouldHideTeamField)
                <input
                    type="hidden"
                    name="team_id"
                    value="{{ $resolvedTeamId }}"
                >
            @else
                <x-form.select-picker
                    name="team_id"
                    label="Team"
                    :options="$teams"
                    :value="$resolvedTeamId"
                    placeholder="Assign to a team"
                    search-placeholder="Search teams"
                    empty-message="No teams found."
                    :multiple="false"
                />
            @endif

            <div class="space-y-2">
                <label
                    for="status"
                    id="status-label"
                >Status</label>

                <div
                    class="flex gap-x-3 mt-4"
                    role="radiogroup"
                    aria-labelledby="status-label"
                >
                    @foreach (App\IdeaStatus::cases() as $statusOption)
                        <button
                            type="button"
                            data-test="button-status-{{ $statusOption->value }}"
                            role="radio"
                            :aria-checked="status === @js($statusOption->value) ? 'true' : 'false'"
                            :class="status === @js($statusOption->value) ? 'btn btn-primary' : 'btn btn-outlined'"
                            aria-label="Set status to {{ $statusOption->label() }}"
                            @click="status = @js($statusOption->value)"
                        >{{ $statusOption->label() }}</button>
                    @endforeach
                </div>

                {{-- Hidden input to submit the status --}}
                <input
                    type="hidden"
                    name="status"
                    x-model="status"
                >
            </div>
            <x-form.form-field
                name="description"
                label="Description"
                type="textarea"
                placeholder="Describe your idea"
                :value="$idea->description"
            />

            {{-- Images --}}
            <x-form.form-field
                name="image"
                label="Featured image"
                type="file"
                accept="image/*"
                multiple
                placeholder="Upload image for your idea"
            />

            @if ($idea->image_path)
                <div
                    class="rounded-lg overflow-hidden -mx-4 -mt-4 sppace-y-2"
                    x-show="!removeImage"
                >
                    <img
                        src="{{ asset('storage/' . $idea->image_path) }}"
                        alt="{{ $idea->title }}"
                        class="p-4 object-cover rounded-lg w-full"
                    >
                </div>

                <button
                    x-show="!removeImage"
                    type="button"
                    class="btn btn-outlined h-10 w-full"
                    data-test="remove-image-button"
                    aria-label="Remove image"
                    @click="removeImage = true"
                >Remove image</button>

                {{-- Hidden input to signal image removal --}}
                <input
                    type="hidden"
                    name="remove_image"
                    x-model="removeImage"
                >
            @endif

            {{-- Steps Component --}}
            <x-modals.idea-modal-steps :steps="$idea->exists ? $idea->steps->map->only('id', 'description', 'completed') : []" />

            {{-- Links Component --}}
            <x-modals.idea-modal-links :links="$idea->exists ? $idea->links : []" />

            <div class="flex justify-end gap-2">
                <button
                    type="button"
                    class="btn btn-outlined h-10"
                    @click="$dispatch('close-modal')"
                    aria-label="Cancel and close dialog"
                >Cancel</button>
                <button
                    type="submit"
                    class="btn h-10"
                    data-test="submit-idea-button"
                >{{ $idea->exists ? 'Update idea' : 'Create idea' }}</button>
            </div>
        </div>
    </x-form.form>
</x-modals.modal>
