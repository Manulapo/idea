<nav class="border-b border-border px-6 fixed top-0 left-0 right-0 bg-background z-50">
    <div class="max-w-7xl mx-auto h-16 flex justify-between items-center">
        <div>

            <a href="/"><x-layout.logo /></a>
        </div>
        <div class="flex gap-x-5 items-center">
            @auth
                <a href="/profile">Profile</a>
                <x-form.form
                    action="/logout"
                    method="DELETE"
                >
                    <button
                        type="submit"
                        class="text-red-400"
                        data-test="logout"
                    >
                        Log Out</button>
                </x-form.form>
            @endauth
            @guest
                <a href="/login">Sign in</a>
                <a
                    href="/register"
                    class="btn"
                >Register</a>
            @endguest
        </div>
    </div>
</nav>
