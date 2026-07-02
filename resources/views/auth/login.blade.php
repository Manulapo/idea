<x-layout.layout>
    <x-form.form-header title="Log in to your account" description="Welcome back! Please enter your details.">
        {{-- form --}}
        <x-form action="/login" method="POST">
            @csrf

            <x-form.form-field name="email" label="Email" type="email" />
            <x-form.form-field name="password" label="Password" type="password" />

            <div class="flex justify-end">
                <button type="submit" class="btn h-10 w-full" data-test="login">Log in</button>
            </div>
        </x-form>

        <p class="mt-6 text-sm text-muted-foreground text-center">
            Don't have an account? <a href="/register" class="font-medium text-primary hover:underline">Register</a>
        </p>
    </x-form.form-header>
</x-layout.layout>
