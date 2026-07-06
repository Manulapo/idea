@props(['title', 'description'])

<div class="flex items-center justify-center px-4 min-h-[calc(100dvh-4rem)]">
    <div class="w-full max-w-md">
        <div class="text-start">
            <h1 class="text-3xl font-bold tracking-tight">{{ $title }}</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                {{ $description }}
            </p>
        </div>
        {{ $slot }}
    </div>
</div>
