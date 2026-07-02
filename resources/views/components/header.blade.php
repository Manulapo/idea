@props(['title', 'description'])

<div class="flex">
    <div class="w-full max-w-md">
        <div class="">
            <h1 class="text-3xl font-bold tracking-tight">{{ $title }}</h1>
            <p class="mt-1 text-sm text-muted-foreground">
                {{ $description }}
            </p>
        </div>
        {{ $slot }}
    </div>
</div>
