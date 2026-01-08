<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        @role('profesor')
            <div class="flex gap-3">
                <a
                    href="{{ route('exams.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600"
                >
                    Crear examen
                </a>
                <a
                    href="#courses-section"
                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600"
                >
                    Gestionar Cursos
                </a>
            </div>
        @endrole

        <div class="grid auto-rows-min gap-4 md:grid-cols-3">
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
            <div class="relative aspect-video overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
                <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
            </div>
        </div>

        <div class="relative h-full flex-1 overflow-hidden rounded-xl border border-neutral-200 dark:border-neutral-700">
            <x-placeholder-pattern class="absolute inset-0 size-full stroke-gray-900/20 dark:stroke-neutral-100/20" />
        </div>

        <!-- SECCIÓN DE CURSOS -->
        <div id="courses-section" class="mt-8">
            @role('profesor')
                <div class="space-y-6">
                    <!-- Crear Curso -->
                    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                        <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Crear Nuevo Curso</h2>
                        <form id="createCourseForm" class="space-y-4">
                            @csrf
                            <div class="grid gap-4 md:grid-cols-2">
                                <div>
                                    <label for="course_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        ID del Curso <span class="text-red-500">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        id="course_id"
                                        name="course_id"
                                        placeholder="MATH101"
                                        required
                                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                    />
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
                                        required
                                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                    />
                                </div>
                            </div>
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Descripción
                                </label>
                                <textarea
                                    id="description"
                                    name="description"
                                    placeholder="Descripción del curso..."
                                    rows="3"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                ></textarea>
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
                                    min="1"
                                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                />
                            </div>
                            <button
                                type="submit"
                                class="inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600"
                            >
                                Crear Curso
                            </button>
                        </form>
                    </div>

                    <!-- Mis Cursos -->
                    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                        <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Mis Cursos</h2>
                        <div id="myCoursesContainer" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                Cargando cursos...
                            </div>
                        </div>
                    </div>

                    <!-- Mis Estudiantes -->
                    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                        <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Mis Estudiantes</h2>
                        <div id="myStudentsContainer" class="space-y-4">
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                Cargando estudiantes...
                            </div>
                        </div>
                    </div>
                </div>
            @endrole

            @role('estudiante')
                <div class="space-y-6">
                    <!-- Buscar Curso -->
                    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                        <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Buscar e Inscribirse en Curso</h2>
                        <form id="searchCourseForm" class="space-y-4">
                            @csrf
                            <div class="flex gap-3">
                                <input
                                    type="text"
                                    id="search_course_id"
                                    placeholder="Ingresa el ID del curso (ej: MATH101)"
                                    class="flex-1 rounded-lg border border-gray-300 px-3 py-2 text-gray-900 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
                                />
                                <button
                                    type="submit"
                                    class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700"
                                >
                                    Buscar
                                </button>
                            </div>
                        </form>
                        <div id="searchResultContainer" class="mt-4"></div>
                    </div>

                    <!-- Mis Cursos -->
                    <div class="rounded-xl border border-neutral-200 bg-white p-6 dark:border-neutral-700 dark:bg-neutral-900">
                        <h2 class="mb-4 text-xl font-bold text-gray-900 dark:text-white">Mis Cursos</h2>
                        <div id="myEnrolledCoursesContainer" class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                Cargando tus cursos...
                            </div>
                        </div>
                    </div>
                </div>
            @endrole
        </div>
    </div>

    <script>
        const apiBase = 'http://127.0.0.1:8001';
        let token = null;
        let currentUser = null;

        // Obtener token del localStorage o sessionStorage
        function getToken() {
            return localStorage.getItem('auth_token') || sessionStorage.getItem('auth_token');
        }

        // Hacer petición a API con autenticación
        async function apiCall(endpoint, method = 'GET', body = null) {
            const token = getToken();
            const headers = {
                'Content-Type': 'application/json',
            };

            if (token) {
                headers['Authorization'] = `Bearer ${token}`;
            }

            const options = {
                method,
                headers,
            };

            if (body) {
                options.body = JSON.stringify(body);
            }

            try {
                const response = await fetch(`${apiBase}${endpoint}`, options);
                return await response.json();
            } catch (error) {
                console.error('Error en API:', error);
                return null;
            }
        }

        // Mostrar alerta
        function showAlert(message, type = 'info') {
            const alertClass = {
                'success': 'bg-green-100 border-green-400 text-green-700',
                'error': 'bg-red-100 border-red-400 text-red-700',
                'info': 'bg-blue-100 border-blue-400 text-blue-700'
            };

            const alertDiv = document.createElement('div');
            alertDiv.className = `border px-4 py-3 rounded relative mb-4 ${alertClass[type]}`;
            alertDiv.textContent = message;
            document.querySelector('#courses-section').insertAdjacentElement('afterbegin', alertDiv);

            setTimeout(() => alertDiv.remove(), 4000);
        }

        // PROFESOR: Crear curso
        @role('profesor')
            document.getElementById('createCourseForm')?.addEventListener('submit', async (e) => {
                e.preventDefault();

                const formData = {
                    course_id: document.getElementById('course_id').value,
                    name: document.getElementById('name').value,
                    description: document.getElementById('description').value,
                    max_students: document.getElementById('max_students').value || null
                };

                const result = await apiCall('/api/courses', 'POST', formData);

                if (result && result.message) {
                    showAlert('Curso creado exitosamente', 'success');
                    document.getElementById('createCourseForm').reset();
                    loadMyTeacherCourses();
                } else {
                    showAlert('Error al crear curso', 'error');
                }
            });

            // Cargar mis cursos (profesor)
            async function loadMyTeacherCourses() {
                const result = await apiCall('/api/my-students');
                const container = document.getElementById('myCoursesContainer');

                if (result && result.courses && result.courses.length > 0) {
                    container.innerHTML = result.courses.map(course => `
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                            <h3 class="font-bold text-gray-900 dark:text-white">${course.name}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">ID: ${course.course_id}</p>
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">${course.description || 'Sin descripción'}</p>
                            <div class="mt-3 flex items-center justify-between text-sm">
                                <span class="text-gray-600 dark:text-gray-400">Estudiantes: ${course.students?.length || 0}</span>
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="text-center py-8 text-gray-500 dark:text-gray-400 col-span-full">No tienes cursos creados</div>';
                }
            }

            // Cargar mis estudiantes (profesor)
            async function loadMyStudents() {
                const result = await apiCall('/api/my-students');
                const container = document.getElementById('myStudentsContainer');

                if (result && result.courses && result.courses.length > 0) {
                    let html = '';
                    result.courses.forEach(course => {
                        if (course.students && course.students.length > 0) {
                            html += `
                                <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                                    <h3 class="font-bold text-gray-900 dark:text-white">${course.name}</h3>
                                    <div class="mt-3 space-y-2">
                                        ${course.students.map(student => `
                                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                                👤 ${student.name} (${student.email})
                                            </div>
                                        `).join('')}
                                    </div>
                                </div>
                            `;
                        }
                    });
                    container.innerHTML = html || '<div class="text-center py-8 text-gray-500 dark:text-gray-400">No tienes estudiantes</div>';
                } else {
                    container.innerHTML = '<div class="text-center py-8 text-gray-500 dark:text-gray-400">No tienes estudiantes</div>';
                }
            }

            // Cargar datos al iniciar
            loadMyTeacherCourses();
            loadMyStudents();
        @endrole

        // ESTUDIANTE: Buscar e inscribirse
        @role('estudiante')
            document.getElementById('searchCourseForm')?.addEventListener('submit', async (e) => {
                e.preventDefault();

                const courseId = document.getElementById('search_course_id').value;
                const result = await apiCall('/api/courses/search', 'POST', { course_id: courseId });
                const container = document.getElementById('searchResultContainer');

                if (result && result.id) {
                    container.innerHTML = `
                        <div class="rounded-lg border border-green-200 bg-green-50 p-4 dark:border-green-700 dark:bg-green-900">
                            <h3 class="font-bold text-gray-900 dark:text-white">${result.name}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">ID: ${result.course_id}</p>
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">${result.description || 'Sin descripción'}</p>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Profesor: ${result.teacher?.name || 'Desconocido'}</p>
                            <button
                                onclick="enrollInCourse(${result.id})"
                                class="mt-4 inline-flex items-center justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-emerald-700"
                            >
                                Inscribirse
                            </button>
                        </div>
                    `;
                } else {
                    container.innerHTML = '<div class="text-center py-4 text-red-500">Curso no encontrado</div>';
                }
            });

            // Inscribirse en curso
            async function enrollInCourse(courseId) {
                const result = await apiCall(`/api/courses/${courseId}/enroll`, 'POST', { course_id: courseId });

                if (result && result.message) {
                    showAlert('¡Inscripción exitosa!', 'success');
                    document.getElementById('searchCourseForm').reset();
                    document.getElementById('searchResultContainer').innerHTML = '';
                    loadMyEnrolledCourses();
                } else {
                    showAlert('Error al inscribirse', 'error');
                }
            }

            // Cargar mis cursos inscritos
            async function loadMyEnrolledCourses() {
                const result = await apiCall('/api/my-courses');
                const container = document.getElementById('myEnrolledCoursesContainer');

                if (result && result.courses && result.courses.length > 0) {
                    container.innerHTML = result.courses.map(course => `
                        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-gray-800">
                            <h3 class="font-bold text-gray-900 dark:text-white">${course.name}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">ID: ${course.course_id}</p>
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">${course.description || 'Sin descripción'}</p>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-sm font-medium text-emerald-600 dark:text-emerald-400">Inscrito</span>
                                <button
                                    onclick="dropCourse(${course.id})"
                                    class="text-sm text-red-600 hover:text-red-800 dark:text-red-400"
                                >
                                    Abandonar
                                </button>
                            </div>
                        </div>
                    `).join('');
                } else {
                    container.innerHTML = '<div class="text-center py-8 text-gray-500 dark:text-gray-400 col-span-full">No estás inscrito en ningún curso</div>';
                }
            }

            // Abandonar curso
            async function dropCourse(courseId) {
                if (confirm('¿Estás seguro que deseas abandonar este curso?')) {
                    const result = await apiCall('/api/courses/drop', 'POST', { course_id: courseId });

                    if (result && result.message) {
                        showAlert('Has abandonado el curso', 'success');
                        loadMyEnrolledCourses();
                    } else {
                        showAlert('Error al abandonar el curso', 'error');
                    }
                }
            }

            // Cargar datos al iniciar
            loadMyEnrolledCourses();
        @endrole
    </script>
</x-layouts.app>
