<x-layouts.app :title="__('Cursos')">
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Cursos Disponibles</h2>
            @role('profesor')
                <a
                    href="{{ route('courses.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700"
                >
                    + Crear Curso
                </a>
            @endrole
        </div>

        @if (session('success'))
            <div class="rounded-lg bg-green-50 p-4 text-green-700 dark:bg-green-900 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="rounded-lg bg-red-50 p-4 text-red-700 dark:bg-red-900 dark:text-red-200">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
            @forelse ($courses as $course)
                <div class="rounded-lg border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                    <div class="mb-4">
                        <span class="inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            {{ $course->course_id }}
                        </span>
                    </div>
                    <h3 class="mb-2 text-lg font-bold text-gray-900 dark:text-white">{{ $course->name }}</h3>
                    <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ Str::limit($course->description, 100) }}
                    </p>

                    <div class="mb-4 space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <p>👤 Profesor: {{ $course->teacher->name }}</p>
                        <p>👥 Estudiantes: {{ $course->students()->count() }}{{ $course->max_students ? ' / ' . $course->max_students : '' }}</p>
                    </div>

                    <div class="flex gap-2">
                        <a
                            href="{{ route('courses.show', $course) }}"
                            class="flex-1 rounded-lg bg-blue-600 px-3 py-2 text-center text-sm font-medium text-white transition hover:bg-blue-700"
                        >
                            Ver Detalles
                        </a>
                        @role('profesor')
                            @can('update', $course)
                                <a
                                    href="{{ route('courses.edit', $course) }}"
                                    class="flex-1 rounded-lg bg-yellow-600 px-3 py-2 text-center text-sm font-medium text-white transition hover:bg-yellow-700"
                                >
                                    Editar
                                </a>
                                <form action="{{ route('courses.destroy', $course) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button
                                        type="submit"
                                        onclick="return confirm('¿Estás seguro que deseas eliminar este curso?')"
                                        class="w-full rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-red-700"
                                    >
                                        Eliminar
                                    </button>
                                </form>
                            @endcan
                        @endrole
                        @role('estudiante')
                            @if ($course->students->contains(auth()->user()))
                                <form action="{{ route('courses.drop', $course) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full rounded-lg bg-red-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-red-700"
                                    >
                                        Abandonar
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('courses.enroll', $course) }}" method="POST" class="flex-1">
                                    @csrf
                                    <button
                                        type="submit"
                                        class="w-full rounded-lg bg-emerald-600 px-3 py-2 text-sm font-medium text-white transition hover:bg-emerald-700"
                                    >
                                        Inscribirse
                                    </button>
                                </form>
                            @endif
                        @endrole
                    </div>
                </div>
            @empty
                <div class="col-span-full rounded-lg bg-gray-50 p-8 text-center text-gray-500 dark:bg-gray-900 dark:text-gray-400">
                    No hay cursos disponibles aún
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $courses->links() }}
        </div>
    </div>
</x-layouts.app>
