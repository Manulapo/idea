<nav class="border-b border-border px-6 fixed top-0 left-0 right-0 bg-background z-50">
    <div class="max-w-7xl mx-auto h-16 flex justify-between items-center">
        <div>
            <a href="/"><x-layout.logo /></a>
        </div>
        <div class="flex gap-x-5 items-center">
            @auth
                <div
                    x-data="{ showProfileMenu: false }"
                    class="relative flex gap-x-5 items-center"
                >
                    <div class="hover:bg-black/20 p-2 rounded-lg"><a
                            href="/teams"
                            class="flex items-center"
                        > <iconify-icon
                                icon="mdi:people"
                                class="inline-block mr-2"
                            ></iconify-icon>Teams</a></div>
                    <div
                        class="cursor-pointer flex items-center"
                        @click="showProfileMenu = !showProfileMenu"
                        @click.outside="showProfileMenu = false"
                        data-test="profile-menu-trigger"
                    >
                        <iconify-icon
                            icon="mdi:account"
                            class="inline-block mr-2"
                        ></iconify-icon>
                        <x-user-avatar
                            :user="Auth::user()"
                            size="10"
                            :withName="true"
                        />
                    </div>

                    <x-menu
                        show="showProfileMenu"
                        class="absolute right-0 top-12"
                    >
                        <li class="hover:bg-black/20 p-2 rounded-lg"><a
                                href="/profile"
                                class="flex items-center"
                            > <iconify-icon
                                    icon="mdi:account"
                                    class="inline-block mr-2"
                                ></iconify-icon>Profile</a></li>

                        <li class="hover:bg-red-500/20 p-2 rounded-lg">
                            <x-form.form
                                action="/logout"
                                method="DELETE"
                                class="m-0! space-y-0!"
                            >
                                <button
                                    type="submit"
                                    class="text-red-400 text-left w-full"
                                    data-test="logout"
                                >
                                    <iconify-icon
                                        icon="mdi:logout"
                                        class="inline-block mr-2"
                                    ></iconify-icon>
                                    Log Out
                                </button>
                            </x-form.form>
                        </li>
                    </x-menu>
                </div>
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
