<x-layouts.app :title="$course->name">
    <div class="max-w-4xl mx-auto space-y-6">
        <a href="{{ route('courses.index') }}" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
            ← Volver a cursos
        </a>

        <div class="rounded-xl border border-neutral-200 bg-white p-8 dark:border-neutral-700 dark:bg-neutral-900">
            <div class="mb-4">
                <span class="inline-block rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                    {{ $course->course_id }}
                </span>
            </div>

            <h1 class="mb-4 text-3xl font-bold text-gray-900 dark:text-white">{{ $course->name }}</h1>

            <div class="mb-6 space-y-3 text-gray-600 dark:text-gray-400">
                <p><strong>Profesor:</strong> {{ $course->teacher->name }}</p>
                <p><strong>Descripción:</strong> {{ $course->description ?? 'Sin descripción' }}</p>
                <p><strong>Estudiantes inscritos:</strong> {{ $course->students()->count() }}{{ $course->max_students ? ' / ' . $course->max_students : ' (Ilimitado)' }}</p>
                <p><strong>Estado:</strong>
                    @if ($course->is_active)
                        <span class="inline-block rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800 dark:bg-green-900 dark:text-green-200">
                            ✅ Activo
                        </span>
                    @else
                        <span class="inline-block rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800 dark:bg-red-900 dark:text-red-200">
                            ❌ Inactivo
                        </span>
                    @endif
                </p>
            </div>

            <div class="flex gap-3 flex-wrap">
                @role('profesor')
                    @can('update', $course)
                        <a
                            href="{{ route('courses.edit', $course) }}"
                            class="inline-flex items-center justify-center rounded-lg bg-yellow-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-yellow-700"
                        >
                            ✏️ Editar
                        </a>
                        <form action="{{ route('courses.destroy', $course) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button
                                type="submit"
                                onclick="return confirm('¿Estás seguro que deseas eliminar este curso? Esta acción no se puede deshacer.')"
                                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700"
                            >
                                🗑️ Eliminar
                            </button>
                        </form>
                    @endcan
                @endrole

                @role('estudiante')
                    @if ($course->students->contains(auth()->user()))
                        <div class="inline-flex items-center rounded-lg bg-green-100 px-4 py-2 text-sm font-medium text-green-800 dark:bg-green-900 dark:text-green-200">
                            ✅ Estás inscrito en este curso
                        </div>
                        <form action="{{ route('courses.drop', $course) }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                onclick="return confirm('¿Estás seguro que deseas abandonar este curso?')"
                                class="inline-flex items-center justify-center rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700"
                            >
                                ❌ Abandonar curso
                            </button>
                        </form>
                    @else
                        <form action="{{ route('courses.enroll', $course) }}" method="POST">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-emerald-700"
                            >
                                ✅ Inscribirse en este curso
                            </button>
                        </form>
                    @endif
                @endrole
            </div>
        </div>

        @role('profesor')
            @can('update', $course)
                <div class="rounded-xl border border-neutral-200 bg-white p-8 dark:border-neutral-700 dark:bg-neutral-900">
                    <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Estudiantes Inscritos</h2>
                    @if ($course->students->count() > 0)
                        <div class="space-y-2">
                            @foreach ($course->students as $student)
                                <div class="flex justify-between items-center p-3 bg-gray-50 rounded dark:bg-gray-800">
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $student->name }}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $student->email }}</p>
                                    </div>
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded dark:bg-blue-900 dark:text-blue-200">
                                        Inscrito
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">Sin estudiantes inscritos aún</p>
                    @endif
                </div>
            @endcan
        @endrole
    </div>
</x-layouts.app>
