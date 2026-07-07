@props(['name' => 'create-team', 'title' => 'Create a team', 'type' => 'create', 'team' => new App\Models\Team(), 'users' => []])

@php
    $formAction = $type === 'create' ? route('teams.store') : route('teams.update', $team->id);
    $formMethod = $type === 'create' ? 'POST' : 'PATCH';
@endphp

<x-modals.modal
    name="{{ $name }}"
    title="{{ $title }}"
    data-test="{{ $name }}-modal"
>
    <x-form.form
        action="{{ $formAction }}"
        method="{{ $formMethod }}"
        aria-label="{{ $type === 'create' ? 'Create a new team' : 'Edit team' }}"
        enctype="multipart/form-data"
    >
        <x-form.form-field
            name="name"
            label="Team Name"
            type="text"
            placeholder="Enter a name for your team"
            :value="$team->name"
            required
        />
        <x-form.form-field
            name="description"
            label="Team Description"
            type="textarea"
            placeholder="Enter a description for your team"
            :value="$team->description"
            required
        />

        <x-modals.team-modal-participants
            :team="$team"
            :users="$users"
        />

        <div class="w-full flex justify-end">
            <button
                type="submit"
                class="btn btn-primary mt-4 "
                data-test="{{ $name }}-submit-button"
            >
                {{ $type === 'create' ? 'Create Team' : 'Update Team' }}
            </button>
        </div>

    </x-form.form>
</x-modals.modal>
