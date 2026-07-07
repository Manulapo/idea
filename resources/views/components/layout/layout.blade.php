<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >
    <meta
        http-equiv="X-UA-Compatible"
        content="ie=edge"
    >
    <title>Idea.</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
</head>

<body class="text-foreground bg-background">
    <x-layout.nav />
    <main class="max-w-7xl mx-auto px-6 py-5 mt-16">
        {{ $slot }}
    </main>

    {{-- with "success" from the login response + the message value --}}
    @session('success')
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition.opacity.duration.300ms
            class="bg-primary text-black px-4 py-3 fixed bottom-4 right-4 rounded-lg"
        >
            {{ $value }}</div>
    @endsession

    {{-- error session --}}
    @session('error')
        <div
            x-data="{ show: true }"
            x-init="setTimeout(() => show = false, 3000)"
            x-show="show"
            x-transition.opacity.duration.300ms
            class="bg-red-500 text-white px-4 py-3 fixed bottom-4 right-4 rounded-lg"
        >
            {{ $value }}</div>
    @endsession
</body>

</html>
