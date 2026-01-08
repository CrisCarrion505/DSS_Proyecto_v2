<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Mostrar formulario para crear curso
     */
    public function create()
    {
        return view('courses.create');
    }

    /**
     * Guardar nuevo curso
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|string|unique:courses',
            'name' => 'required|string',
            'description' => 'nullable|string',
            'max_students' => 'nullable|integer|min:1'
        ]);

        Course::create([
            'course_id' => $validated['course_id'],
            'name' => $validated['name'],
            'description' => $validated['description'],
            'max_students' => $validated['max_students'],
            'teacher_id' => auth()->id()
        ]);

        return redirect()->route('courses.index')->with('success', 'Curso creado exitosamente');
    }

    /**
     * Listar cursos
     */
    public function index()
    {
        $courses = Course::active()->paginate(12);
        return view('courses.index', compact('courses'));
    }

    /**
     * Ver detalles de un curso
     */
    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }

    /**
     * Formulario de edición
     */
    public function edit(Course $course)
    {
        // Verificar que sea el profesor del curso
        if ($course->teacher_id !== auth()->id()) {
            return redirect()->route('courses.index')->with('error', 'No tienes permiso para editar este curso');
        }

        return view('courses.edit', compact('course'));
    }

    /**
     * Actualizar curso
     */
    public function update(Request $request, Course $course)
    {
        // Verificar que sea el profesor del curso
        if ($course->teacher_id !== auth()->id()) {
            return redirect()->route('courses.index')->with('error', 'No tienes permiso para actualizar este curso');
        }

        $validated = $request->validate([
            'name' => 'sometimes|string',
            'description' => 'sometimes|string',
            'max_students' => 'sometimes|integer|min:1'
        ]);

        $course->update($validated);

        return redirect()->route('courses.show', $course)->with('success', 'Curso actualizado');
    }

    /**
     * Eliminar curso
     */
    public function destroy(Course $course)
    {
        // Verificar que sea el profesor del curso
        if ($course->teacher_id !== auth()->id()) {
            return redirect()->route('courses.index')->with('error', 'No tienes permiso para eliminar este curso');
        }

        $course->delete();

        return redirect()->route('courses.index')->with('success', 'Curso eliminado');
    }

    /**
     * Inscribirse en curso
     */
    public function enroll(Request $request, Course $course)
    {
        $student = auth()->user();

        if ($student->courses()->whereKey($course->id)->exists()) {
            return back()->with('error', 'Ya estás inscrito en este curso');
        }

        if ($course->max_students && $course->students()->count() >= $course->max_students) {
            return back()->with('error', 'Curso lleno');
        }

        $student->courses()->attach($course->id, [
            'status' => 'enrolled',
            'enrolled_at' => now()
        ]);

        return redirect()->route('courses.index')->with('success', 'Inscripción exitosa');
    }

    /**
     * Abandonar curso
     */
    public function drop(Request $request, Course $course)
    {
        auth()->user()->courses()->detach($course->id);

        return redirect()->route('courses.index')->with('success', 'Has abandonado el curso');
    }
}
