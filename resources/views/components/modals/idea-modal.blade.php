@props(['name' => 'create-idea', 'title' => 'Create an idea', 'type' => 'create', 'idea' => new App\Models\Idea()])

@php
    $formAction = $type === 'create' ? route('ideas.store') : route('ideas.update', $idea->id);
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
        aria-label="{{ $type === 'create' ? 'Create a new idea' : 'Edit idea' }}"
        enctype="multipart/form-data"
    >
        <div
            class="space-y-6"
            x-data="{
                status: '{{ old('status', $idea->status) }}',
                newLink: '',
                links: @js(old('links', $idea->exists ? $idea->links : [])),
                newStep: '',
                steps: @js(old('steps', $idea->exists ? $idea->steps->map->only('id', 'description', 'completed') : [])),
                removeImage: false
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

            {{-- Steps --}}
            <div>
                <fieldset class="space-y-3">
                    <legend class="text-sm font-medium text-foreground">Actionable Steps</legend>

                    {{-- Add new step input --}}
                    <div class="flex gap-x-2 items-center">
                        <input
                            type="text"
                            placeholder="Enter a Step for the idea"
                            id="new-step"
                            autocomplete="off"
                            class="input flex-1"
                            spellcheck="false"
                            x-model="newStep"
                            data-test="new-step"
                            aria-label="New step"
                            @keydown.enter.prevent="if(newStep.trim()) { steps.push({description: newStep.trim(), completed: false}); newStep = ''; }"
                        />
                        <button
                            type="button"
                            :disabled="newStep.trim().length === 0"
                            class="btn btn-outlined h-10"
                            @click="steps.push({description: newStep.trim(), completed: false}); newStep = ''"
                            data-test="add-step-button"
                            aria-label="Add step to list"
                        >
                            <iconify-icon
                                icon="lucide:plus"
                                class="rotate-45"
                                width="24"
                                height="24"
                                aria-hidden="true"
                            ></iconify-icon>
                            <span
                                class="sr-only"
                                data-test="add-step"
                            >Add step</span>
                        </button>
                    </div>

                    {{-- Display added steps --}}
                    <div
                        role="list"
                        aria-live="polite"
                        aria-label="Added steps"
                    >
                        <template
                            x-for="(step, index) in steps"
                            :key="step.id || index"
                        >
                            <div
                                class="flex gap-x-2 items-center border border-border rounded-lg p-3 my-2"
                                role="listitem"
                            >
                                <input
                                    type="hidden"
                                    :name="'steps[' + index + '][description]'"
                                    :value="step.description"
                                />
                                <input
                                    type="hidden"
                                    :name="'steps[' + index + '][completed]'"
                                    :value="step.completed ? '1' : '0'"
                                />
                                <span
                                    class="flex-1 text-sm truncate line-clamp-1"
                                    x-text="step.description"
                                    :aria-label="'Step: ' + step.description"
                                ></span>
                                <button
                                    type="button"
                                    @click="steps.splice(index, 1)"
                                    data-test="remove-step-button"
                                    :aria-label="'Remove step ' + step.description"
                                >
                                    <iconify-icon
                                        icon="lucide:x"
                                        width="20"
                                        height="20"
                                        aria-hidden="true"
                                    ></iconify-icon>
                                    <span
                                        class="sr-only"
                                        data-test="remove-step"
                                    >Remove step</span>
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
                        <input
                            type="url"
                            placeholder="Enter a URL"
                            id="new-link"
                            autocomplete="url"
                            class="input flex-1"
                            spellcheck="false"
                            x-model="newLink"
                            data-test="new-link"
                            aria-label="New link URL"
                            @keydown.enter.prevent="if(newLink.trim()) { links.push(newLink.trim()); newLink = ''; }"
                        />
                        <button
                            type="button"
                            :disabled="newLink.trim().length === 0"
                            class="btn btn-outlined h-10"
                            @click="links.push(newLink.trim()); newLink = ''"
                            data-test="add-link-button"
                            aria-label="Add link to list"
                        >
                            <iconify-icon
                                icon="lucide:plus"
                                class="rotate-45"
                                width="24"
                                height="24"
                                aria-hidden="true"
                            ></iconify-icon>
                            <span
                                class="sr-only"
                                data-test="add-link"
                            >Add link</span>
                        </button>
                    </div>

                    {{-- Display added links --}}
                    <div
                        role="list"
                        aria-live="polite"
                        aria-label="Added links"
                    >
                        <template
                            x-for="(link, index) in links"
                            :key="index"
                        >
                            <div
                                class="flex gap-x-2 items-center border border-border rounded-lg p-3 my-2"
                                role="listitem"
                            >
                                <input
                                    type="hidden"
                                    name="links[]"
                                    :value="link"
                                />
                                <span
                                    class="flex-1 text-sm truncate line-clamp-1"
                                    x-text="link"
                                    :aria-label="'Link: ' + link"
                                ></span>
                                <button
                                    type="button"
                                    @click="links.splice(index, 1)"
                                    data-test="remove-link-button"
                                    :aria-label="'Remove link ' + link"
                                >
                                    <iconify-icon
                                        icon="lucide:x"
                                        width="20"
                                        height="20"
                                        aria-hidden="true"
                                    ></iconify-icon>
                                    <span class="sr-only">Remove link</span>
                                </button>
                            </div>
                        </template>
                    </div>

                </fieldset>
            </div>

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
