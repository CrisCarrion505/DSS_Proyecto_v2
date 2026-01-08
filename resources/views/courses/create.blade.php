<x-layouts.app :title="__('Crear Curso')">
    <div class="max-w-2xl mx-auto">
        <div class="rounded-xl border border-neutral-200 bg-white p-8 dark:border-neutral-700 dark:bg-neutral-900">
            <h2 class="mb-6 text-2xl font-bold text-gray-900 dark:text-white">Crear Nuevo Curso</h2>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 p-4 text-red-700 dark:bg-red-900 dark:text-red-200">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('courses.store') }}" method="POST" class="space-y-6">
                @csrf

                <div>
                    <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        ID del Curso <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="course_id"
                        name="course_id"
                        placeholder="MATH101"
                        value="{{ old('course_id') }}"
                        required
                        class="mt-2 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    />
                    @error('course_id')
                        <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Nombre del Curso <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        placeholder="Matemáticas Básicas"
                        value="{{ old('name') }}"
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
                        placeholder="Descripción del curso..."
                        rows="4"
                        class="mt-2 block w-full rounded-lg border border-gray-300 px-4 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <span class="text-sm text-red-600 dark:text-red-400">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label for="max_students" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Máximo de Estudiantes (opcional)
                    </label>
                    <input
                        type="number"
                        id="max_students"
                        name="max_students"
                        placeholder="30"
                        value="{{ old('max_students') }}"
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
                        Crear Curso
                    </button>
                    <a
                        href="{{ route('courses.index') }}"
                        class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-6 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800"
                    >
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
