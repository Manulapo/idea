@props(['team' => new App\Models\Team(), 'users' => []])

@php
    $selectedParticipants = collect(old('participants', $team->exists ? $team->users->pluck('id')->all() : []))
        ->map(fn($id) => (string) $id)
        ->all();

    $participantOptions = collect($users)
        ->map(fn($user) => ['id' => (string) $user->id, 'name' => $user->name])
        ->values();
@endphp

<div
    class="space-y-3"
    x-data="{
        open: false,
        query: '',
        dropdownStyles: '',
        menuId: 'participants-menu',
        options: @js($participantOptions),
        selected: @js($selectedParticipants),
        toggleOpen() {
            this.open = !this.open;
    
            if (!this.open) {
                return;
            }
    
            this.$nextTick(() => {
                this.updateDropdownPosition();
                this.$refs.searchInput?.focus();
            });
        },
        close() {
            this.open = false;
        },
        updateDropdownPosition() {
            const trigger = this.$refs.trigger;
    
            if (!trigger) {
                return;
            }
    
            const triggerRect = trigger.getBoundingClientRect();
    
            this.dropdownStyles = `position: fixed; left: ${triggerRect.left}px; top: ${triggerRect.bottom + 8}px; width: ${triggerRect.width}px;`;
        },
        handleWindowClick(event) {
            if (!this.open) {
                return;
            }
    
            const trigger = this.$refs.trigger;
            const menu = this.$refs.menu;
    
            if (trigger?.contains(event.target) || menu?.contains(event.target)) {
                return;
            }
    
            this.close();
        },
        isSelected(id) {
            return this.selected.includes(String(id));
        },
        toggle(id) {
            const normalizedId = String(id);
    
            if (this.isSelected(normalizedId)) {
                this.selected = this.selected.filter((value) => value !== normalizedId);
                return;
            }
    
            this.selected.push(normalizedId);
        },
        remove(id) {
            const normalizedId = String(id);
            this.selected = this.selected.filter((value) => value !== normalizedId);
        },
        clearAll() {
            this.selected = [];
        },
        get filteredOptions() {
            if (!this.query.trim()) {
                return this.options;
            }
    
            const searchQuery = this.query.toLowerCase();
    
            return this.options.filter((option) => option.name.toLowerCase().includes(searchQuery));
        },
        selectedLabel(id) {
            const selectedOption = this.options.find((option) => option.id === String(id));
            return selectedOption ? selectedOption.name : id;
        }
    }"
    @keydown.escape.window="close()"
    @click.window="handleWindowClick($event)"
    @resize.window="updateDropdownPosition()"
    @scroll.window="updateDropdownPosition()"
>
    <label
        for="participants-search"
        class="label text-start"
    >Team Participants</label>

    <div class="relative">
        <div
            class="input h-auto min-h-10 cursor-pointer text-left"
            x-ref="trigger"
            role="button"
            tabindex="0"
            @click="toggleOpen()"
            @keydown.enter.prevent="toggleOpen()"
            @keydown.space.prevent="toggleOpen()"
            :aria-expanded="open ? 'true' : 'false'"
            :aria-controls="menuId"
        >
            <template x-if="selected.length === 0">
                <span class="text-muted-foreground">Select participants for your team</span>
            </template>

            <div
                x-show="selected.length > 0"
                class="flex flex-wrap gap-2"
                x-cloak
            >
                <template
                    x-for="participantId in selected"
                    :key="participantId"
                >
                    <span
                        class="inline-flex items-center gap-1 rounded-full border border-border bg-background px-3 py-1 text-sm"
                    >
                        <span x-text="selectedLabel(participantId)"></span>
                        <button
                            type="button"
                            class="form-muted-icon"
                            @click.stop="remove(participantId)"
                            aria-label="Remove participant"
                        >x</button>
                    </span>
                </template>
            </div>
        </div>

        <template x-teleport="body">
            <div
                :id="menuId"
                x-show="open"
                x-transition.origin.top.left
                x-cloak
                x-ref="menu"
                :style="dropdownStyles"
                class="z-70 space-y-3 rounded-xl border border-border bg-card p-3 shadow-2xl"
            >
                <input
                    id="participants-search"
                    type="text"
                    class="input h-9"
                    x-model="query"
                    x-ref="searchInput"
                    placeholder="Search participants"
                >

                <div class="max-h-56 space-y-1 overflow-y-auto pr-1">
                    <template x-if="filteredOptions.length === 0">
                        <p class="px-3 py-2 text-sm text-muted-foreground">No participants found.</p>
                    </template>

                    <template
                        x-for="option in filteredOptions"
                        :key="option.id"
                    >
                        <button
                            type="button"
                            class="flex w-full items-center justify-between rounded-lg border border-transparent px-3 py-2 text-left text-sm transition hover:border-border hover:bg-background"
                            @click="toggle(option.id)"
                        >
                            <span x-text="option.name"></span>
                            <span
                                class="inline-flex h-5 w-5 items-center justify-center rounded-md border border-border bg-background"
                                :class="isSelected(option.id) ? 'border-primary text-primary' : 'text-transparent'"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 20 20"
                                    fill="currentColor"
                                    class="h-4 w-4"
                                >
                                    <path
                                        fill-rule="evenodd"
                                        d="M16.704 5.29a1 1 0 0 1 .006 1.414l-8 8a1 1 0 0 1-1.42-.004l-4-4a1 1 0 0 1 1.414-1.414l3.294 3.293 7.296-7.295a1 1 0 0 1 1.41.006Z"
                                        clip-rule="evenodd"
                                    />
                                </svg>
                            </span>
                        </button>
                    </template>
                </div>

                <div class="flex justify-end">
                    <button
                        type="button"
                        class="btn btn-outlined h-8"
                        @click="clearAll()"
                    >Clear</button>
                </div>
            </div>
        </template>
    </div>

    <div>
        <template
            x-for="participantId in selected"
            :key="`selected-${participantId}`"
        >
            <input
                type="hidden"
                name="participants[]"
                :value="participantId"
            >
        </template>
    </div>

    @error('participants')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
    @error('participants.*')
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror
</div>
