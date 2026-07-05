<x-layout.layout>
    <x-form.form-header
        title="Edit Profile"
        description="Update your account information."
    >
        {{-- form --}}
        <x-form.form
            action="/profile"
            method="PATCH"
        >
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
    </x-form.form-header>
</x-layout.layout>
