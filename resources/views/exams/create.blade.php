<x-layouts.app :title="__('Crear Examen')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="p-6">
            <div class="mb-8">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">🧠 Crear Examen</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Genera exámenes automáticos con IA</p>
            </div>

            <div class="max-w-2xl mx-auto bg-white dark:bg-neutral-800 p-8 rounded-xl shadow-sm border border-neutral-200 dark:border-neutral-700">
                @if ($errors->any())
                    <div class="mb-6 rounded-lg bg-red-50 p-4 dark:bg-red-900/30">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <ul class="list-disc space-y-1 pl-5 text-sm text-red-700 dark:text-red-200">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <form action="{{ route('exams.store') }}" method="POST" class="space-y-6">
                    @csrf

                    {{-- ✅ Curso --}}
                    <div>
                        <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">📚 Curso</label>
                        <select id="course_id" name="course_id" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white sm:text-sm py-2">
                            <option value="" disabled {{ old('course_id') ? '' : 'selected' }}>
                                Selecciona un curso
                            </option>

                            @foreach(($courses ?? []) as $course)
                                <option value="{{ $course->id }}"
                                    {{ (string)old('course_id') === (string)$course->id ? 'selected' : '' }}>
                                    {{ $course->course_id }} — {{ $course->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Este examen se publicará en el curso seleccionado.</p>
                    </div>

                    {{-- ✅ Nivel --}}
                    <div>
                        <label for="level" class="block text-sm font-medium text-gray-700 dark:text-gray-300">⚡ Nivel</label>
                        <select id="level" name="level" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white sm:text-sm py-2">
                            <option value="basico" {{ old('level','basico')==='basico' ? 'selected' : '' }}>Básico</option>
                            <option value="intermedio" {{ old('level')==='intermedio' ? 'selected' : '' }}>Intermedio</option>
                            <option value="avanzado" {{ old('level')==='avanzado' ? 'selected' : '' }}>Avanzado</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        {{-- ✅ Calificación máxima --}}
                        <div>
                            <label for="score_max" class="block text-sm font-medium text-gray-700 dark:text-gray-300">📊 Calificación máxima</label>
                            <input type="number" id="score_max" name="score_max"
                                   value="{{ old('score_max') }}" min="1" max="100" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white sm:text-sm py-2"
                                   placeholder="Ej: 20">
                        </div>

                        {{-- ✅ Número de preguntas --}}
                        <div>
                            <label for="questions_count" class="block text-sm font-medium text-gray-700 dark:text-gray-300">📝 Número de preguntas</label>
                            <input type="number" id="questions_count" name="questions_count"
                                   value="{{ old('questions_count') }}" min="1" max="50" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white sm:text-sm py-2"
                                   placeholder="Ej: 10">
                        </div>
                    </div>

                    {{-- ✅ Tema --}}
                    <div>
                        <label for="topic" class="block text-sm font-medium text-gray-700 dark:text-gray-300">🎯 Tema del examen</label>
                        <input type="text" id="topic" name="topic"
                               value="{{ old('topic') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white sm:text-sm py-2"
                               placeholder="Ej: Programación web, Matemáticas, Historia...">
                    </div>

                    {{-- ✅ Descripción (opcional) --}}
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">🧾 Descripción (opcional)</label>
                        <textarea id="description" name="description" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-neutral-900 dark:border-neutral-700 dark:text-white sm:text-sm"
                                  placeholder="Ej: Examen parcial, incluye unidad 1 y 2...">{{ old('description') }}</textarea>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                                class="flex w-full justify-center rounded-md border border-transparent bg-indigo-600 py-3 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-all">
                            ✨ Generar Examen con IA
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-layouts.app>
