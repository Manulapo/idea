@props(['steps' => [], 'oldSteps' => null])

<div>
    <fieldset class="space-y-3">
        <legend class="text-sm font-medium text-foreground">Actionable Steps</legend>

        {{-- Add new step input --}}
        <div class="flex gap-x-2 items-center items-center">
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
                :class="{
                    'h-10 flex items-center': true,
                    'btn btn-outlined disabled': newStep.trim().length === 0,
                    'btn btn-outlined': newStep.trim()
                        .length > 0
                }"
                @click="steps.push({description: newStep.trim(), completed: false}); newStep = ''"
                data-test="add-step-button"
                aria-label="Add step to list"
            >
                <iconify-icon
                    icon="lucide:plus"
                    width="20"
                    height="20"
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
                        class="flex items-center justify-center"
                    >
                        <iconify-icon
                            icon="lucide:x"
                            width="20"
                            height="20"
                            aria-hidden="true"
                            class="text-red-400/50"
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
