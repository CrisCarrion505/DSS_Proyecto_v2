<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;

class CourseEnrollmentController extends Controller
{
    /**
     * Inscribir estudiante en un curso
     * POST /api/courses/{id}/enroll
     */
    public function enroll(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::active()->findOrFail($request->course_id);
        $student = auth()->user();

        // Verificar que sea estudiante
        if (! $student->hasRole('student')) {
            return response()->json(['message' => 'Solo estudiantes pueden inscribirse'], 403);
        }

        // Verificar si ya está inscrito (FIX: sin columna ambigua)
        if ($student->courses()->whereKey($course->id)->exists()) {
            return response()->json(['message' => 'Ya estás inscrito en este curso'], 409);
        }

        // Verificar límite de estudiantes
        if ($course->max_students && $course->students()->count() >= $course->max_students) {
            return response()->json(['message' => 'Curso lleno'], 403);
        }

        // Crear inscripción (con enrolled_at si tu tabla lo tiene)
        CourseEnrollment::create([
            'course_id' => $course->id,
            'user_id' => $student->id,
            'status' => 'enrolled',
            'enrolled_at' => now(),
        ]);

        return response()->json([
            'message' => 'Inscripción exitosa',
            'course' => $course,
        ], 201);
    }


    /**
     * Ver mis cursos (para estudiantes)
     * GET /api/my-courses
     */
    public function myEnrollments()
    {
        $courses = auth()->user()->activeCourses()->get();

        return response()->json([
            'message' => 'Tus cursos',
            'courses' => $courses
        ]);
    }

    /**
     * Abandonar un curso
     * POST /api/courses/drop
     */
    public function drop(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $enrollment = CourseEnrollment::where('course_id', $request->course_id)
                                      ->where('user_id', auth()->id())
                                      ->first();

        if (!$enrollment) {
            return response()->json(
                ['message' => 'No estás inscrito en este curso'],
                404
            );
        }

        $enrollment->delete();

        return response()->json(['message' => 'Has abandonado el curso']);
    }

    /**
     * Ver mis estudiantes (para profesores)
     * GET /api/my-students
     */
    public function myStudents()
    {
        // Obtener solo cursos del profesor autenticado
        $courses = auth()->user()->createdCourses()->with('students')->get();

        return response()->json([
            'message' => 'Tus estudiantes',
            'courses' => $courses
        ]);
    }

    /**
     * Ver estudiantes inscritos en un curso específico
     * GET /api/courses/{id}/students
     */
    public function courseStudents($courseId)
    {
        $course = Course::findOrFail($courseId);

        // Verificar que sea el profesor del curso
        if ($course->teacher_id !== auth()->id()) {
            return response()->json(
                ['message' => 'No autorizado'],
                403
            );
        }

        $students = $course->students()
                          ->where('course_enrollments.status', 'enrolled')
                          ->get();

        return response()->json([
            'message' => 'Estudiantes del curso',
            'course_id' => $course->id,
            'course_name' => $course->name,
            'total_students' => $students->count(),
            'students' => $students
        ]);
    }

    /**
     * Obtener estadísticas de inscripción de un curso
     * GET /api/courses/{id}/enrollment-stats
     */
    public function enrollmentStats($courseId)
    {
        $course = Course::findOrFail($courseId);

        // Verificar que sea el profesor del curso
        if ($course->teacher_id !== auth()->id()) {
            return response()->json(
                ['message' => 'No autorizado'],
                403
            );
        }

        $enrolledCount = $course->students()->where('course_enrollments.status', 'enrolled')->count();
        $completedCount = $course->students()->where('course_enrollments.status', 'completed')->count();
        $droppedCount = $course->students()->where('course_enrollments.status', 'dropped')->count();

        return response()->json([
            'course_id' => $course->id,
            'course_name' => $course->name,
            'max_students' => $course->max_students,
            'enrolled' => $enrolledCount,
            'completed' => $completedCount,
            'dropped' => $droppedCount,
            'total' => $enrolledCount + $completedCount + $droppedCount
        ]);
    }
}
