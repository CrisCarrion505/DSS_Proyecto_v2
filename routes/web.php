<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;
use App\Http\Controllers\FaceController;
use App\Http\Controllers\ExamController;
use App\Http\Controllers\CourseController;

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

    abort(403, 'Tu usuario no tiene rol asignado.');
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
    Route::view('estudiante/dashboard', 'dashboards.estudiante')
        ->middleware('role:estudiante')
        ->name('estudiante.dashboard');

    Route::view('profesor/dashboard', 'dashboards.profesor')
        ->middleware('role:profesor')
        ->name('profesor.dashboard');
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
});

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
