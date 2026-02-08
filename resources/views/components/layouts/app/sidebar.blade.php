<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white dark:bg-zinc-800">
        <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
            <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

            <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
                <x-app-logo />
            </a>
                <flux:navlist.group :heading="__('Platform')" class="grid">
        {{-- Dashboard (todos) --}}
        <flux:navlist.item
            icon="home"
            :href="route('dashboard')"
            :current="request()->routeIs('dashboard')"
            wire:navigate
        >
            {{ __('Dashboard') }}
        </flux:navlist.item>

        {{-- Cursos (profesor y estudiante) --}}
        <flux:navlist.item
            icon="book-open"
            :href="route('courses.index')"
            :current="request()->routeIs('courses.*')"
            wire:navigate
        >
            {{ __('Cursos') }}
        </flux:navlist.item>

        {{-- Estudiante: Reconocimiento + Examen --}}
        @role('estudiante')
            <flux:navlist.item
                icon="face-smile"
                :href="route('examen.show')"
                :current="request()->routeIs('examen.*')"
                wire:navigate
            >
                {{ __('Reconocimiento') }}
            </flux:navlist.item>

            <flux:navlist.item
                icon="academic-cap"
                :href="route('knowledge.index')"
                :current="request()->routeIs('knowledge.index') || request()->routeIs('knowledge.read')"
                wire:navigate
            >
                {{ __('Recursos AI') }}
            </flux:navlist.item>
        @endrole

        {{-- Profesor: Crear curso + Crear examen --}}
        @role('profesor')
            <flux:navlist.item
                icon="plus-circle"
                :href="route('courses.create')"
                :current="request()->routeIs('courses.create')"
                wire:navigate
            >
                {{ __('Crear curso') }}
            </flux:navlist.item>

            <flux:navlist.item
                icon="document-plus"
                :href="route('exams.create')"
                :current="request()->routeIs('exams.*')"
                wire:navigate
            >
                {{ __('Crear examen') }}
            </flux:navlist.item>

            <flux:navlist.item
                icon="sparkles"
                :href="route('knowledge.create')"
                :current="request()->routeIs('knowledge.create') || request()->routeIs('knowledge.preview')"
                wire:navigate
            >
                {{ __('Generar Recurso') }}
            </flux:navlist.item>
        @endrole
    </flux:navlist.group>
            <flux:spacer />
            <flux:navlist variant="outline">


    {{-- Opcional: grupo de configuración (si quieres que settings sea pestaña, no solo en el dropdown) --}}
    <flux:navlist.group :heading="__('Configuración')" class="grid mt-2">
            <flux:navlist.item
                icon="cog"
                :href="route('profile.edit')"
                :current="request()->routeIs('profile.edit')"
                wire:navigate
            >
                {{ __('Perfil') }}
            </flux:navlist.item>

            <flux:navlist.item
                icon="key"
                :href="route('user-password.edit')"
                :current="request()->routeIs('user-password.edit')"
                wire:navigate
            >
                {{ __('Contraseña') }}
            </flux:navlist.item>

            <flux:navlist.item
                icon="paint-brush"
                :href="route('appearance.edit')"
                :current="request()->routeIs('appearance.edit')"
                wire:navigate
            >
                {{ __('Apariencia') }}
            </flux:navlist.item>
        </flux:navlist.group>
    </flux:navlist>


            <!-- Desktop User Menu -->
            <flux:dropdown class="hidden lg:block" position="bottom" align="start">
                <flux:profile
                    :name="auth()->user()->name"
                    :initials="auth()->user()->initials()"
                    icon:trailing="chevrons-up-down"
                    data-test="sidebar-menu-button"
                />

                <flux:menu class="w-[220px]">
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                                    <span
                                        class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white"
                                    >
                                        {{ auth()->user()->initials() }}
                                    </span>
                                </span>

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                                    <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>{{ __('Settings') }}</flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full" data-test="logout-button">
                            {{ __('Log Out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
        </flux:header>

        {{ $slot }}

        @fluxScripts
    </body>
</html>
