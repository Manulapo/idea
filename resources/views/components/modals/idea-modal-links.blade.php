@props(['links' => [], 'oldLinks' => null])

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
                :class="{
                    'h-10 flex items-center': true,
                    'btn btn-outlined disabled': newLink.trim().length === 0,
                    'btn btn-outlined': newLink.trim()
                        .length > 0
                }"
            >
                <iconify-icon
                    icon="lucide:plus"
                    width="20"
                    height="20"
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
                        class="flex items-center justify-center"
                    >
                        <iconify-icon
                            icon="lucide:x"
                            width="20"
                            height="20"
                            aria-hidden="true"
                            class="text-red-400/50"
                        ></iconify-icon>
                        <span class="sr-only">Remove link</span>
                    </button>
                </div>
            </template>
        </div>

    </fieldset>
</div>
