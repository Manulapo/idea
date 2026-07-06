@props(['ideaId'])

<x-card
    is="div"
    class="flex flex-col gap-2 mt-10"
>
    <x-form.form
        action="{{ route('ideas.add-comment', $ideaId) }}"
        method="POST"
        class="flex flex-col gap-2 w-full max-w-full"
    >
        <x-form.form-field
            name="content"
            type="textarea"
            class="w-full max-w-full border-none h-10"
            placeholder="Write your comment here..."
            required
        />
        <button
            type="submit"
            class="btn btn-primary self-end flex items-center"
        >Add Comment
            <iconify-icon
                icon="mdi:send"
                class="ml-2"
            ></iconify-icon>
        </button>
    </x-form.form>
</x-card>
