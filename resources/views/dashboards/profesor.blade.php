<x-layouts.app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6 rounded-xl">

        {{-- =========================
             1) RESUMEN / KPIs
        ========================== --}}
        <div class="grid gap-4 md:grid-cols-4">

            <div class="rounded-xl border border-neutral-200 p-5 dark:border-neutral-700">
                <p class="text-sm text-neutral-500">Mis cursos</p>
                <p class="mt-2 text-3xl font-semibold">{{ $counts['my_courses'] ?? 0 }}</p>
                <p class="mt-1 text-xs text-neutral-400">Cursos vinculados a tu usuario</p>
            </div>

            <div class="rounded-xl border border-neutral-200 p-5 dark:border-neutral-700">
                <p class="text-sm text-neutral-500">Cursos disponibles</p>
                <p class="mt-2 text-3xl font-semibold">{{ $counts['available_courses'] ?? 0 }}</p>
                <p class="mt-1 text-xs text-neutral-400">Solo activos</p>
            </div>

            {{-- Solo para profesor: exámenes creados --}}
            @role('profesor')
            <div class="rounded-xl border border-neutral-200 p-5 dark:border-neutral-700">
                <p class="text-sm text-neutral-500">Exámenes recientes</p>
                <p class="mt-2 text-3xl font-semibold">{{ $counts['my_exams'] ?? 0 }}</p>
                <p class="mt-1 text-xs text-neutral-400">Creados por ti</p>
            </div>
            @endrole

            {{-- Solo para estudiante: resultados --}}
            @role('estudiante')
            <div class="rounded-xl border border-neutral-200 p-5 dark:border-neutral-700">
                <p class="text-sm text-neutral-500">Resultados</p>
                <p class="mt-2 text-3xl font-semibold">{{ $counts['my_results'] ?? 0 }}</p>
                <p class="mt-1 text-xs text-neutral-400">Exámenes enviados</p>
            </div>
            @endrole

            {{-- Caja de estado / tips --}}
            <div class="rounded-xl border border-neutral-200 p-5 dark:border-neutral-700">
                <p class="text-sm text-neutral-500">Estado</p>
                <p class="mt-2 text-sm text-neutral-400 leading-relaxed">
                    @role('profesor')
                        Crea cursos, genera exámenes con IA y publica el examen activo por curso.
                    @endrole
                    @role('estudiante')
                        Revisa tus cursos y rinde el examen activo cuando esté disponible.
                    @endrole
                </p>
            </div>
        </div>


        {{-- =========================
             2) ACCESO RÁPIDO + ALERTAS
        ========================== --}}
        <div class="grid gap-4 md:grid-cols-3">

            <div class="rounded-xl border border-neutral-200 p-6 dark:border-neutral-700 md:col-span-2">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold">Acceso rápido</h2>
                    <span class="text-xs text-neutral-500">EduSecure</span>
                </div>

                <div class="mt-4 flex flex-wrap gap-2">
                    @role('profesor')
                        <a href="{{ route('courses.create') }}"
                           class="rounded-lg bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-700">
                            + Crear curso
                        </a>

                        <a href="{{ route('exams.create') }}"
                           class="rounded-lg bg-emerald-600 px-3 py-2 text-sm text-white hover:bg-emerald-700">
                            + Crear examen (IA)
                        </a>

                        <a href="{{ route('knowledge.create') }}"
                           class="rounded-lg bg-slate-900 px-3 py-2 text-sm text-white hover:bg-slate-800">
                            + Crear lectura de refuerzo
                        </a>

                        <a href="{{ route('courses.index') }}"
                           class="rounded-lg bg-neutral-800 px-3 py-2 text-sm text-white hover:bg-neutral-700">
                            Ver cursos
                        </a>
                    @endrole

                    @role('estudiante')
                        <a href="{{ route('courses.index') }}"
                           class="rounded-lg bg-indigo-600 px-3 py-2 text-sm text-white hover:bg-indigo-700">
                            Ver cursos
                        </a>

                        <a href="{{ route('estudiante.dashboard') }}"
                           class="rounded-lg bg-neutral-800 px-3 py-2 text-sm text-white hover:bg-neutral-700">
                            Mi dashboard
                        </a>
                    @endrole
                </div>

                <div class="mt-4 text-xs text-neutral-400 leading-relaxed">
                    Tip: publica solo 1 examen activo por curso (ya tienes <b>is_active</b> y relación <b>activeExam</b>).
                </div>
            </div>

            <div class="rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
                <h2 class="text-lg font-semibold">Alertas</h2>

                @if(!empty($alerts))
                    <ul class="mt-4 space-y-2 text-sm text-neutral-400">
                        @foreach($alerts as $a)
                            <li class="rounded-lg border border-neutral-200 p-3 dark:border-neutral-700">
                                <p class="font-medium text-neutral-200">{{ $a['title'] ?? 'Aviso' }}</p>
                                <p class="mt-1 text-xs text-neutral-400">{{ $a['body'] ?? '' }}</p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="mt-4 text-sm text-neutral-500">Sin alertas por ahora.</p>
                @endif
            </div>

        </div>


        {{-- =========================
             3) CURSOS RECIENTES + ACCIONES
        ========================== --}}
        <div class="rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-lg font-semibold">Cursos recientes</h2>
                <a href="{{ route('courses.index') }}" class="text-sm text-indigo-400 hover:text-indigo-300">
                    Ver todos →
                </a>
            </div>

            @if(isset($myCourses) && $myCourses->count())
                <div class="grid gap-4 md:grid-cols-3">
                    @foreach($myCourses as $course)
                        <div class="rounded-xl border border-neutral-200 p-4 transition hover:bg-neutral-50 dark:border-neutral-700 dark:hover:bg-neutral-800/40">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs text-neutral-500">{{ $course->course_id }}</p>
                                    <h3 class="text-base font-semibold">{{ $course->name }}</h3>
                                </div>

                                <a href="{{ route('courses.show', $course) }}"
                                   class="rounded-lg bg-neutral-800 px-3 py-2 text-xs text-white hover:bg-neutral-700">
                                    Ver detalles
                                </a>
                            </div>

                            <p class="mt-2 line-clamp-2 text-sm text-neutral-500">
                                {{ $course->description ?? 'Sin descripción' }}
                            </p>

                            {{-- PROFESOR: estado de examen activo --}}
                            @role('profesor')
                                <div class="mt-4 rounded-lg border border-neutral-200 p-3 dark:border-neutral-700">
                                    <p class="text-xs text-neutral-500">Examen activo</p>

                                    @if($course->activeExam)
                                        <p class="mt-1 text-sm text-neutral-200 font-medium">
                                            {{ $course->activeExam->titulo }}
                                        </p>

                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <a href="{{ route('courses.show', $course) }}"
                                               class="rounded-lg bg-indigo-600 px-3 py-2 text-xs text-white hover:bg-indigo-700">
                                                Gestionar curso
                                            </a>

                                            <form action="{{ route('exams.publish', $course->activeExam) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit"
                                                        class="rounded-lg bg-emerald-600 px-3 py-2 text-xs text-white hover:bg-emerald-700">
                                                    Activar
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <p class="mt-1 text-sm text-neutral-500">No hay examen publicado</p>
                                        <a href="{{ route('exams.create') }}"
                                           class="mt-3 inline-flex rounded-lg bg-emerald-600 px-3 py-2 text-xs text-white hover:bg-emerald-700">
                                            Crear examen
                                        </a>
                                    @endif
                                </div>
                            @endrole

                            {{-- ESTUDIANTE: rendir examen si existe --}}
                            @role('estudiante')
                                <p class="mt-3 text-xs text-neutral-500">
                                    Profesor: {{ $course->teacher->name ?? '—' }}
                                </p>

                                @if($course->activeExam)
                                    <a href="{{ route('courses.examen.take', $course) }}"
                                       class="mt-3 inline-flex w-full justify-center rounded-lg bg-emerald-600 px-3 py-2 text-sm text-white hover:bg-emerald-700">
                                        Rendir examen
                                    </a>
                                @else
                                    <p class="mt-3 text-sm text-neutral-500">No hay examen activo</p>
                                @endif
                            @endrole
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-neutral-500">No hay cursos para mostrar.</p>
            @endif
        </div>


        {{-- =========================
             4) ACTIVIDAD RECIENTE (LLENA EL “VACÍO”)
        ========================== --}}
        <div class="grid gap-4 md:grid-cols-2">

            <div class="rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
                <h2 class="text-lg font-semibold">Actividad reciente</h2>

                <div class="mt-4 space-y-3 text-sm text-neutral-400">
                    @if(!empty($recentExams) && count($recentExams))
                        <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                            <p class="text-xs text-neutral-500">Últimos exámenes</p>
                            <ul class="mt-2 space-y-1">
                                @foreach($recentExams as $ex)
                                    <li class="flex items-center justify-between gap-3">
                                        <span class="text-neutral-200">{{ $ex->titulo }}</span>
                                        <span class="text-xs text-neutral-500">{{ $ex->created_at?->diffForHumans() }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                            <p class="text-neutral-500">Aún no hay exámenes recientes.</p>
                        </div>
                    @endif

                    @if(!empty($recentResults) && count($recentResults))
                        <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                            <p class="text-xs text-neutral-500">Últimos resultados</p>
                            <ul class="mt-2 space-y-1">
                                @foreach($recentResults as $r)
                                    <li class="flex items-center justify-between gap-3">
                                        <span class="text-neutral-200">
                                            {{ $r->exam->titulo ?? 'Examen' }}
                                        </span>
                                        <span class="text-xs text-neutral-500">
                                            {{ $r->percentage }}%
                                            @if($r->status === 'flagged')
                                                <span class="ml-1 text-red-400">(flag)</span>
                                            @endif
                                        </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                            <p class="text-neutral-500">Aún no hay resultados recientes.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="rounded-xl border border-neutral-200 p-6 dark:border-neutral-700">
                <h2 class="text-lg font-semibold">Guía rápida</h2>

                <div class="mt-4 space-y-3 text-sm text-neutral-400">
                    @role('profesor')
                        <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                            <p class="text-neutral-200 font-medium">1) Crea el curso</p>
                            <p class="mt-1 text-xs">Define cupo y vincula estudiantes.</p>
                        </div>
                        <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                            <p class="text-neutral-200 font-medium">2) Genera lectura y examen</p>
                            <p class="mt-1 text-xs">IA + examen por tema y nivel.</p>
                        </div>
                        <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                            <p class="text-neutral-200 font-medium">3) Publica el examen activo</p>
                            <p class="mt-1 text-xs">Solo 1 activo por curso (ya lo haces con publish).</p>
                        </div>
                    @endrole

                    @role('estudiante')
                        <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                            <p class="text-neutral-200 font-medium">1) Entra al curso</p>
                            <p class="mt-1 text-xs">Lee instrucciones y confirma cámara.</p>
                        </div>
                        <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                            <p class="text-neutral-200 font-medium">2) Rinde el examen</p>
                            <p class="mt-1 text-xs">Sistema monitorea pestaña y rostro.</p>
                        </div>
                        <div class="rounded-lg border border-neutral-200 p-4 dark:border-neutral-700">
                            <p class="text-neutral-200 font-medium">3) Mira tu resultado</p>
                            <p class="mt-1 text-xs">Se guarda en exam_results.</p>
                        </div>
                    @endrole
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
