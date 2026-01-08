<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    @include('partials.head')
</head>

<body class="min-h-screen antialiased auth-bg">
    <div class="flex min-h-svh items-center justify-center p-6 md:p-10">
        <div class="w-full max-w-sm">
            <a href="{{ route('home') }}"
               class="mb-6 flex flex-col items-center gap-2 font-medium"
               wire:navigate>
                <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-white/10">
                    <x-app-logo-icon class="size-9 fill-current text-white" />
                </span>
                <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
            </a>

            <div class="auth-card">
                {{ $slot }}
            </div>
        </div>
    </div>

    @fluxScripts
</body>
</html>
