<x-layout.layout>
    <x-form.form-header
        title="Edit Profile"
        description="Update your account information."
    >

        {{-- image --}}
        {{-- form --}}
        <x-form.form
            action="/profile"
            method="PATCH"
            enctype="multipart/form-data"
        >
            <x-card class="my-6">
                <div class="flex justify-between items-center gap-4">
                    <div class="relative group">
                        @if ($user->image_path)
                            <x-user-avatar
                                :user="$user"
                                size="24"
                                class="cursor-pointer"
                            />
                        @else
                            {{-- placeholder with User initial --}}
                            <div
                                class="w-24 h-24 rounded-full bg-primary flex items-center justify-center text-2xl font-bold text-white">
                                {{ strtoupper(substr($user->name, 0, 1)) }}</div>
                        @endif
                        <button
                            form="delete-image-form"
                            data-test="remove-image-button"
                            aria-label="Remove image"
                            class="btn btn-outlined flex items-center group-hover:opacity-100 opacity-0 transition-opacity duration-300 absolute right-0 top-0 bg-background p-2 rounded-full"
                        >
                            <iconify-icon
                                icon="lucide:x"
                                class="text-red-400"
                            ></iconify-icon>
                        </button>
                    </div>
                    <x-form.form-field
                        name="image_path"
                        type="file"
                    />
                </div>
            </x-card>
            <x-form.form-field
                name="name"
                label="Name"
                type="text"
                value="{{ $user->name }}"
            />
            <x-form.form-field
                name="email"
                label="Email"
                type="email"
                value="{{ $user->email }}"
            />
            <x-form.form-field
                name="password"
                label="New Password"
                type="password"
            />

            <x-form.form-field
                name="password_confirmation"
                label="Confirm New Password"
                type="password"
            />

            <div class="flex justify-end">
                <button
                    type="submit"
                    class="btn h-10 w-full"
                >Update Profile</button>
            </div>
        </x-form.form>

        <x-form.form
            action="/profile"
            method="DELETE"
            class="mt-6"
        >
            <div class="flex justify-end">
                <button
                    type="submit"
                    class="btn btn-outlined h-10 w-full text-red-400"
                >Delete Account</button>
            </div>
        </x-form.form>
    </x-form.form-header>

    {{-- delete image --}}
    <x-form.form
        id="delete-image-form"
        action="{{ route('profile.delete-profile-image') }}"
        method="DELETE"
    ></x-form.form>
</x-layout.layout>
