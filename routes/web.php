<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\CourseController;
use App\Models\Course;
use App\Models\Exam;
use App\Http\Controllers\KnowledgeModuleController;

Route::get('/', function () {
    return view('welcome');
})->name('home');

/**
 * Dashboard principal: redirige según rol
 */
Route::get('dashboard', function () {
    $user = auth()->user();

    if ($user->hasRole('profesor')) {
        return redirect()->route('profesor.dashboard');
    }

    if ($user->hasRole('estudiante')) {
        return redirect()->route('estudiante.dashboard');
    }
    $myCourses = Course::where('teacher_id', $user->id)->latest()->get();

    // Contadores
    $counts = [
        'my_courses' => $myCourses->count(),
        'available_courses' => Course::where('is_active', true)->count(),
        'my_exams' => Exam::where('teacher_id', $user->id)->count(),
    ];

    // Exámenes recientes
    $recentExams = Exam::where('teacher_id', $user->id)
        ->latest()
        ->take(5)
        ->get();

    $alerts = [];

    return view('dashboard', compact(
        'myCourses',
        'counts',
        'recentExams',
        'alerts'
    ));


})
->middleware(['auth', 'verified'])
->name('dashboard');

/**
 * Rutas autenticadas (settings)
 */
Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

        Route::middleware('role:profesor')->group(function () {
        Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    });

    // Rutas SOLO para ESTUDIANTES
    Route::middleware('role:estudiante')->group(function () {
        Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
        Route::post('/courses/{course}/drop', [CourseController::class, 'drop'])->name('courses.drop');
    });

    // Rutas públicas (ambos roles) - AL FINAL, después de rutas más específicas
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');
});

/**
 * Dashboards por rol (protegidos con Spatie role middleware)
 */
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('estudiante/dashboard', function () {
        $user = auth()->user();

        $myCourses = $user->courses()
            ->with('teacher')
            ->with(['activeExam'])         // si existe relación teacher en Course
            ->latest()
            ->take(6)
            ->get();

        $counts = [
            'my_courses' => $user->courses()->count(),
            'available_courses' => Course::active()->count(), // si tienes scopeActive en Course
        ];

        return view('dashboards.estudiante', compact('myCourses', 'counts'));
    })->middleware('role:estudiante')->name('estudiante.dashboard');


    Route::get('profesor/dashboard', function () {
        $userId = auth()->id();

        $myCourses = Course::where('teacher_id', $userId)
            ->latest()
            ->take(6)
            ->get();

        $counts = [
            'my_courses' => Course::where('teacher_id', $userId)->count(),
            // opcional: exámenes del profe si quieres
            // 'my_exams' => Exam::where('teacher_id', $userId)->count(),
        ];

        // Fetch recent Knowledge Sessions for this teacher's modules
        $recentSessions = \App\Models\KnowledgeSession::whereHas('module', function($q) use ($userId){
                $q->where('teacher_id', $userId);
            })
            ->with(['student', 'module'])
            ->latest('updated_at')
            ->take(10)
            ->get();

        return view('dashboards.profesor', compact('myCourses', 'counts', 'recentSessions'));
    })->middleware('role:profesor')->name('profesor.dashboard');

});

/**
 * ===== RUTAS DE CURSOS =====
 */
Route::middleware(['auth', 'verified'])->group(function () {

    // Rutas públicas (ambos roles)
    Route::get('/courses', [CourseController::class, 'index'])->name('courses.index');
    Route::get('/courses/{course}', [CourseController::class, 'show'])->name('courses.show');

    // Rutas SOLO para PROFESORES
    Route::middleware('role:profesor')->group(function () {
        Route::get('/courses/create', [CourseController::class, 'create'])->name('courses.create');
        Route::post('/courses', [CourseController::class, 'store'])->name('courses.store');
        Route::get('/courses/{course}/edit', [CourseController::class, 'edit'])->name('courses.edit');
        Route::put('/courses/{course}', [CourseController::class, 'update'])->name('courses.update');
        Route::delete('/courses/{course}', [CourseController::class, 'destroy'])->name('courses.destroy');
    });

    // Rutas SOLO para ESTUDIANTES
    Route::middleware('role:estudiante')->group(function () {
        Route::post('/courses/{course}/enroll', [CourseController::class, 'enroll'])->name('courses.enroll');
        Route::post('/courses/{course}/drop', [CourseController::class, 'drop'])->name('courses.drop');
    });
});

/**
 * Face (lo dejo público como lo tenías)
 */
Route::get('/face', function () {
    return view('face');
})->name('face');

Route::post('/face/register', [FaceController::class, 'register']);
Route::post('/face/verify', [FaceController::class, 'verify']);

/**
 * Examen: SOLO estudiante
 */
Route::middleware(['auth', 'verified', 'role:estudiante'])->group(function () {
    Route::get('/examen', [ExamController::class, 'show'])->name('examen.show');
    Route::post('/examen/evaluar', [ExamController::class, 'evaluate'])->name('examen.evaluate');
});

/**
 * Creación de exámenes: SOLO profesor
 */
Route::middleware(['auth', 'verified', 'role:profesor'])->group(function () {
    Route::get('/exams/create', [ExamController::class, 'create'])->name('exams.create');
    Route::post('/exams', [ExamController::class, 'store'])->name('exams.store');
});

/**
 * API Sanctum (si la usas después)
 */
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/exams/{id}', [ExamController::class, 'show']);
    Route::post('/exams/{id}/evaluate', [ExamController::class, 'evaluate']);
});

Route::get('/courses-dashboard', function () {
    return view('courses-dashboard');
});


Route::middleware(['auth','verified','role:profesor'])->group(function () {
    Route::patch('/exams/{exam}/publish', [ExamController::class, 'publish'])
        ->name('exams.publish');
});


Route::middleware(['auth','verified','role:estudiante'])->group(function () {
    Route::get('/courses/{course}/examen', [ExamController::class, 'take'])
        ->name('courses.examen.take');


    Route::post('/courses/{course}/examen/submit', [ExamController::class, 'submit'])
        ->name('courses.examen.submit');

     Route::get('/courses/{course}/examen/result', [ExamController::class, 'result'])
        ->name('courses.examen.result');
});

Route::middleware(['auth','verified','role:profesor'])->group(function () {
    Route::get('/courses/{course}/exams/create', [ExamController::class, 'createForCourse'])
        ->name('courses.exams.create');
});


Route::middleware(['auth','verified'])->group(function () {

    // PROFESOR
    Route::middleware('role:profesor')->group(function () {
        Route::get('/knowledge/create', [KnowledgeModuleController::class, 'create'])->name('knowledge.create');
        Route::post('/knowledge', [KnowledgeModuleController::class, 'store'])->name('knowledge.store');
        Route::get('/knowledge/{module}/preview', [KnowledgeModuleController::class, 'preview'])->name('knowledge.preview');
        Route::patch('/knowledge/{module}/toggle', [KnowledgeModuleController::class, 'toggle'])->name('knowledge.toggle');
        Route::get('/knowledge/session/{session}/report', [KnowledgeModuleController::class, 'report'])->name('knowledge.report');
    });

    // ESTUDIANTE
    Route::middleware('role:estudiante')->group(function () {
        Route::get('/knowledge/{module}/read', [KnowledgeModuleController::class, 'read'])->name('knowledge.read');

        // métricas durante lectura
        Route::post('/knowledge-sessions/{session}/heartbeat', [KnowledgeModuleController::class, 'heartbeat'])->name('knowledge.heartbeat');

        // terminar lectura
        Route::post('/knowledge-sessions/{session}/finish', [KnowledgeModuleController::class, 'finish'])->name('knowledge.finish');
        Route::get('/knowledge', [KnowledgeModuleController::class, 'index'])
            ->name('knowledge.index')
            ->middleware('role:estudiante');
    });

});


