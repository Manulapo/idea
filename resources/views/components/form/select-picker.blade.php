@props([
    'name',
    'label' => '',
    'displayLabel' => null,
    'options' => [],
    'value' => null,
    'multiple' => true,
    'placeholder' => 'Select an option',
    'searchPlaceholder' => 'Search options',
    'emptyMessage' => 'No options found.',
    'optionValueKey' => 'id',
    'optionLabelKey' => 'name',
    'withAvatars' => false,
    'optionAvatarKey' => 'avatar',
])

@php
    $hasLabel = filled($label);
    $selectedValues = collect(old($name, $value ?? ($multiple ? [] : null)));

    if (!$multiple) {
        $selectedValues = $selectedValues->filter(fn($selectedValue) => filled($selectedValue))->take(1);
    }

    $selectedValues = $selectedValues->map(fn($selectedValue) => (string) $selectedValue)->values()->all();

    $normalizedOptions = collect($options)
        ->map(function ($option) use ($optionValueKey, $optionLabelKey, $optionAvatarKey) {
            return [
                'id' => (string) data_get($option, $optionValueKey),
                'name' => (string) data_get($option, $optionLabelKey),
                'avatar' => data_get($option, $optionAvatarKey),
            ];
        })
        ->filter(fn(array $option) => filled($option['id']) && filled($option['name']))
        ->values()
        ->all();

    $inputId = (string) \Illuminate\Support\Str::slug($name, '-');
    $menuId = $inputId . '-menu';
@endphp

<div
    {{ $attributes->merge(['class' => $hasLabel ? 'space-y-3' : '']) }}
    x-data="{
        open: false,
        query: '',
        dropdownStyles: '',
        optionsMaxHeight: 224,
        menuId: @js($menuId),
        options: @js($normalizedOptions),
        selected: @js($selectedValues),
        displayLabel: @js($displayLabel),
        multiple: @js($multiple),
        withAvatars: @js($withAvatars),
        toggleOpen() {
            this.open = !this.open;
    
            if (!this.open) {
                return;
            }
    
            this.$nextTick(() => {
                this.updateDropdownPosition();
                this.$refs.searchInput?.focus({ preventScroll: true });
            });
        },
        close() {
            this.open = false;
            this.query = '';
            this.dropdownStyles = '';
        },
        updateDropdownPosition() {
            if (!this.open) {
                return;
            }
    
            const trigger = this.$refs.trigger;
            const menu = this.$refs.menu;
    
            if (!trigger || !menu) {
                return;
            }
    
            const viewportPadding = 16;
            const dropdownOffset = 8;
            const triggerRect = trigger.getBoundingClientRect();
            const menuHeight = menu.offsetHeight || 320;
            const menuWidth = Math.min(triggerRect.width, window.innerWidth - viewportPadding * 2);
            const spaceBelow = window.innerHeight - triggerRect.bottom - dropdownOffset - viewportPadding;
            const spaceAbove = triggerRect.top - dropdownOffset - viewportPadding;
            const shouldOpenUp = spaceBelow < 260 && spaceAbove > spaceBelow;
            const availableVerticalSpace = Math.max(180, shouldOpenUp ? spaceAbove : spaceBelow);
            const renderedMenuHeight = Math.min(menuHeight, availableVerticalSpace);
    
            const unclampedTop = shouldOpenUp ?
                triggerRect.top - dropdownOffset - renderedMenuHeight :
                triggerRect.bottom + dropdownOffset;
    
            const maxTop = window.innerHeight - viewportPadding - renderedMenuHeight;
            const top = Math.max(viewportPadding, Math.min(unclampedTop, maxTop));
            const left = Math.max(
                viewportPadding,
                Math.min(triggerRect.left, window.innerWidth - viewportPadding - menuWidth)
            );
    
            this.optionsMaxHeight = Math.max(96, availableVerticalSpace - 120);
    
            this.dropdownStyles = `position: fixed; left: ${left}px; top: ${top}px; width: ${menuWidth}px; max-height: ${availableVerticalSpace}px;`;
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
    
            if (this.multiple) {
                if (this.isSelected(normalizedId)) {
                    this.selected = this.selected.filter((value) => value !== normalizedId);
                    return;
                }
    
                this.selected.push(normalizedId);
                return;
            }
    
            this.selected = this.isSelected(normalizedId) ? [] : [normalizedId];
            this.close();
            this.$nextTick(() => this.$dispatch('select-picker-change'));
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
            return selectedOption ? selectedOption.name : (this.displayLabel ?? id);
        }
    }"
    @keydown.escape.window="close()"
    @close-modal.window="close()"
    @click.window="handleWindowClick($event)"
    @resize.window="updateDropdownPosition()"
    @scroll.window="updateDropdownPosition()"
>
    @if ($hasLabel)
        <label
            for="{{ $inputId }}-search"
            class="daisy-label text-start"
        >
            <span class="daisy-label-text text-foreground font-semibold">{{ $label }}</span>
        </label>
    @endif

    <div class="relative">
        <div
            class="daisy-select daisy-select-bordered !bg-card !border-border !text-foreground !rounded-xl h-auto min-h-10 py-2 cursor-pointer text-left flex items-center justify-between w-full"
            x-ref="trigger"
            role="button"
            tabindex="0"
            @click="toggleOpen()"
            @keydown.enter.prevent="toggleOpen()"
            @keydown.space.prevent="toggleOpen()"
            :aria-expanded="open ? 'true' : 'false'"
            :aria-controls="menuId"
        >
            <div class="flex flex-wrap gap-2 pr-4">
                <template x-if="selected.length === 0">
                    <span class="text-muted-foreground">{{ $placeholder }}</span>
                </template>

                <template x-if="!multiple && selected.length > 0">
                    <span x-text="selectedLabel(selected[0])"></span>
                </template>

                <div
                    x-show="multiple && selected.length > 0"
                    class="flex flex-wrap gap-2"
                    x-cloak
                >
                    <template
                        x-for="selectedId in selected"
                        :key="selectedId"
                    >
                        <span class="daisy-badge daisy-badge-outline gap-1.5 py-3 pl-3 pr-2 text-sm">
                            <span x-text="selectedLabel(selectedId)"></span>
                            <button
                                type="button"
                                class="daisy-btn daisy-btn-ghost daisy-btn-xs min-h-0 h-4 w-4 p-0 text-muted-foreground hover:text-foreground"
                                @click.stop="remove(selectedId)"
                                :aria-label="`Remove ${selectedLabel(selectedId)}`"
                            >
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="h-3 w-3"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </span>
                    </template>
                </div>
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
                class="z-70 flex flex-col space-y-3 overflow-hidden rounded-xl border border-border bg-card p-3 shadow-2xl"
            >
                <input
                    id="{{ $inputId }}-search"
                    type="text"
                    class="daisy-input daisy-input-bordered daisy-input-sm w-full"
                    x-model="query"
                    x-ref="searchInput"
                    placeholder="{{ $searchPlaceholder }}"
                >

                <ul
                    class="daisy-menu daisy-menu-sm p-0 space-y-0.5 overflow-y-auto pr-1 w-full"
                    :style="`max-height: ${optionsMaxHeight}px;`"
                >
                    <template x-if="filteredOptions.length === 0">
                        <li><span class="text-muted-foreground">{{ $emptyMessage }}</span></li>
                    </template>

                    <template
                        x-for="option in filteredOptions"
                        :key="option.id"
                    >
                        <li>
                            <button
                                type="button"
                                class="flex items-center justify-between rounded-lg px-3 py-2 text-left"
                                @click="toggle(option.id)"
                            >
                                <span class="flex items-center gap-2">
                                    <template x-if="withAvatars">
                                        <span>
                                            <template x-if="option.avatar">
                                                <img
                                                    :src="option.avatar"
                                                    :alt="option.name"
                                                    class="w-6 h-6 rounded-full object-cover"
                                                />
                                            </template>
                                            <template x-if="!option.avatar">
                                                <span
                                                    class="w-6 h-6 rounded-full bg-primary flex items-center justify-center text-white text-xs font-bold"
                                                    x-text="option.name.charAt(0).toUpperCase()"
                                                ></span>
                                            </template>
                                        </span>
                                    </template>
                                    <span x-text="option.name"></span>
                                </span>
                                <template x-if="multiple">
                                    <input
                                        type="checkbox"
                                        class="daisy-checkbox daisy-checkbox-primary daisy-checkbox-sm pointer-events-none"
                                        :checked="isSelected(option.id)"
                                        tabindex="-1"
                                    />
                                </template>
                                <template x-if="!multiple">
                                    <span
                                        x-show="isSelected(option.id)"
                                        class="text-primary flex items-center justify-center"
                                    >
                                        <svg
                                            xmlns="http://www.w3.org/2000/svg"
                                            viewBox="0 0 20 20"
                                            fill="currentColor"
                                            class="h-5 w-5"
                                        >
                                            <path
                                                fill-rule="evenodd"
                                                d="M16.704 5.29a1 1 0 0 1 .006 1.414l-8 8a1 1 0 0 1-1.42-.004l-4-4a1 1 0 0 1 1.414-1.414l3.294 3.293 7.296-7.295a1 1 0 0 1 1.41.006Z"
                                                clip-rule="evenodd"
                                            />
                                        </svg>
                                    </span>
                                </template>
                            </button>
                        </li>
                    </template>
                </ul>

                <div class="flex justify-end pt-1 border-t border-border">
                    <button
                        type="button"
                        class="daisy-btn daisy-btn-outline daisy-btn-xs"
                        @click="clearAll()"
                    >Clear</button>
                </div>
            </div>
        </template>
    </div>

    @if ($multiple)
        <div>
            <template
                x-for="selectedId in selected"
                :key="`selected-${selectedId}`"
            >
                <input
                    type="hidden"
                    name="{{ $name }}[]"
                    :value="selectedId"
                >
            </template>
        </div>
    @else
        <input
            type="hidden"
            name="{{ $name }}"
            :value="selected[0] ?? ''"
        >
    @endif

    @error($name)
        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
    @enderror

    @if ($multiple)
        @error($name . '.*')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
        @enderror
    @endif
</div>
