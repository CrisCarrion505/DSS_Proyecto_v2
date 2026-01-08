<x-layouts.app :title="'Editar ' . $course->name">
    <div class="max-w-2xl mx-auto">
        <div class="rounded-xl border border-neutral-200 bg-white p-8 dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">Editar Curso</h2>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-700 dark:bg-red-900 dark:text-red-200">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('courses.update', $course) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nombre del Curso <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $course->name) }}"
                        required
                        class="mt-2 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    />
                    @error('name')
                        <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Descripción
                    </label>
                    <textarea
                        id="description"
                        name="description"
                        rows="4"
                        class="mt-2 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    >{{ old('description', $course->description) }}</textarea>
                    @error('description')
                        <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="max_students" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Máximo de Estudiantes
                    </label>
                    <input
                        type="number"
                        id="max_students"
                        name="max_students"
                        value="{{ old('max_students', $course->max_students) }}"
                        min="1"
                        class="mt-2 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    />
                    @error('max_students')
                        <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <div class="flex gap-3">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-6 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700"
                    >
                        Guardar Cambios
                    </button>
                    <a
                        href="{{ route('courses.show', $course) }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
