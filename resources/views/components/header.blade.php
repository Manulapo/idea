@props(['title', 'description'])

<div class="flex mt-8">
    <div class="w-full max-w-md">
        <h1 class="text-3xl font-bold tracking-tight">{{ $title }}</h1>
        <p class="mt-1 text-sm text-muted-foreground">
            {{ $description }}
        </p>
        {{ $slot }}
    </div>
</div>
