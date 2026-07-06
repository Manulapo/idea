@props(['comments'])

<div>
    @forelse ($comments as $comment)
        <x-card
            x-data="{ showCommentMenu: false, editComment: false }"
            class="gap-4 space-y-2 mb-2"
        >
            {{-- three dots menu --}}
            <div class="flex justify-end relative">
                @if (auth()->user() && auth()->user()->id === $comment->user_id)
                    <iconify-icon
                        class="text-muted-foreground cursor-pointer absolute right-0 top-0"
                        icon="mdi:dots-vertical"
                        width="20"
                        height="20"
                        @click="showCommentMenu = !showCommentMenu"
                        @click.outside="showCommentMenu = false"
                        aria-label="Comment options"
                    ></iconify-icon>
                    <x-menu
                        show="showCommentMenu"
                        class="dropdown dropdown-end"
                    >

                        <li>
                            <x-form.form
                                action="{{ route('comments.update', $comment->id) }}"
                                method="PATCH"
                            >
                                <button
                                    type="submit"
                                    class="flex items-center"
                                    @click.prevent="editComment = !editComment; showCommentMenu = false"
                                >
                                    <iconify-icon
                                        icon="mdi:pencil"
                                        class="inline-block mr-2"
                                    ></iconify-icon>
                                    Edit</button>
                            </x-form.form>
                        </li>
                        <li>
                            <x-form.form
                                action="{{ route('comments.destroy', $comment->id) }}"
                                method="DELETE"
                            >
                                <button
                                    type="submit"
                                    class="text-red-500 flex items-center"
                                >
                                    <iconify-icon
                                        icon="mdi:delete"
                                        class="inline-block mr-2"
                                    ></iconify-icon>
                                    Delete</button>
                            </x-form.form>
                        </li>

                    </x-menu>
                @endif
            </div>

            <div class="flex items-center gap-2 mb-4">
                <x-user-avatar
                    :user="$comment->user"
                    size="6"
                />
                <span
                    class="text-sm text-muted-foreground">{{ $comment->user->name === auth()->user()->name ? $comment->user->name . ' (You)' : $comment->user->name }}</span>
            </div>

            <div class="flex flex-col">
                <p
                    x-cloak
                    x-show="!editComment"
                    class="text-foreground max-w-none"
                >{{ $comment->content }}</p>
            </div>
            <x-form.form
                action="{{ route('comments.update', $comment->id) }}"
                method="PATCH"
                class="w-full max-w-full"
            >
                <x-form.form-field
                    name="content"
                    class="border-none"
                    x-cloak
                    x-show="editComment"
                    value="{{ $comment->content }}"
                >{{ $comment->content }}</x-form.form-field>
                <div class="w-full">
                    <button
                        type="submit"
                        class="btn btn-primary self-end flex items-center ml-auto"
                        x-cloak
                        x-show="editComment"
                    >Update Comment
                        <iconify-icon
                            icon="mdi:send"
                            class="ml-2"
                        ></iconify-icon>
                    </button>
                </div>
            </x-form.form>

            <p
                class="text-xs text-muted-foreground text-end"
                x-cloak
                x-show="!editComment"
            >
                {{ $comment->updated_at->diffForHumans() ?? $comment->created_at->diffForHumans() }}
            </p>
        </x-card>
    @empty
        <x-card>
            <p class="text-sm text-muted-foreground">No comments yet. Be the first to comment.</p>
        </x-card>
    @endforelse
</div>
