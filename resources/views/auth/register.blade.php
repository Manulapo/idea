<x-layout.layout>
    <x-form.form-header title="Register an account" description="Start tracking your ideas today.">
        {{-- form --}}
        <x-form action="/register" method="POST">
            @csrf

            <x-form.form-field name="name" label="Name" type="text" />
            <x-form.form-field name="email" label="Email" type="email" />
            <x-form.form-field name="password" label="Password" type="password" />
            <x-form.form-field name="password_confirmation" label="Confirm Password" type="password" />

            <div class="flex justify-end">
                <button type="submit" class="btn h-10 w-full">Create account</button>
            </div>
        </x-form>

        <p class="mt-6 text-sm text-muted-foreground text-center">
            Already have an account? <a href="/login" class="font-medium text-primary hover:underline">Log
                in</a>
        </p>
    </x-form.form-header>
</x-layout.layout>
