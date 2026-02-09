<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Exam;
use App\Models\Course;
use App\Models\ExamResult;
class ExamController extends Controller
{
    /**
     * Mostrar formulario para crear examen (GET /exams/create)
     */
    public function create()
    {
        $courses = Course::where('teacher_id', auth()->id())
        ->where('is_active', true)
        ->get();

        return view('exams.create', compact('courses'));
    }

    /**
     * Generar examen automáticamente con Gemini (POST /exams)
     */

  public function store(Request $request)
{
    // 1. Validar datos del formulario
    $data = $request->validate([
        'score_max'       => 'required|integer|min:1',
        'questions_count' => 'required|integer|min:1|max:50',
        'topic'           => 'required|string|max:255',
        'level'           => 'nullable|string|in:basico,intermedio,avanzado',
        'course_id'       => 'nullable|exists:courses,id',
    ]);

    $nivel = $data['level'] ?? 'basico';
    $puntajePorPregunta = max(1, intdiv($data['score_max'], $data['questions_count']));

    $prompt = "Genera {$data['questions_count']} preguntas de '{$data['topic']}' en JSON. Nivel: {$nivel}. Cada pregunta debe tener: 'texto', 'opciones'(array de 4 strings), 'correcta'(0-3), 'puntaje':{$puntajePorPregunta}. Responde SOLO JSON válido:
{
 \"titulo\": \"Examen de {$data['topic']}\",
 \"score_max\": {$data['score_max']},
 \"preguntas\": [...]
}";

    // ✅ MODELO ESTABLE QUE FUNCIONA (confirmado en 2026)
    $response = Http::timeout(30)
        ->withHeaders(['Content-Type' => 'application/json'])
        ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . config('services.gemini.key'), [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt]
                    ]
                ]
            ],
            'generationConfig' => [
                'temperature' => 0.1,
            ],
        ]);

    if ($response->failed()) {
        \Log::error('Gemini API Error', [
            'status' => $response->status(),
            'body' => $response->body()
        ]);

        return back()
            ->withInput()
            ->withErrors([
                'api' => 'Error Gemini: ' . $response->status(),
            ]);
    }

    $content = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
    $content = trim(preg_replace('/```json|```|```json\n?/s', '', $content));

    // Extraer JSON
    $start = strpos($content, '{');
    $end = strrpos($content, '}');
    $jsonContent = ($start !== false && $end !== false && $end > $start)
        ? substr($content, $start, $end - $start + 1)
        : $content;

    $exam = json_decode($jsonContent, true);

    if ($exam === null || !isset($exam['preguntas']) || count($exam['preguntas']) === 0) {
        return back()
            ->withInput()
            ->withErrors([
                'api' => 'Gemini no generó JSON válido.',
            ]);
    }

    // ✅ Guardar
    $savedExam = Exam::create([
        'course_id'       => $data['course_id'] ?? null,
        'teacher_id'      => auth()->id(),
        'titulo'          => $exam['titulo'] ?? "Examen de {$data['topic']}",
        'description'     => $exam['description'] ?? null,
        'score_max'       => $exam['score_max'] ?? $data['score_max'],
        'questions_count' => $data['questions_count'],
        'topic'           => $data['topic'],
        'level'           => $nivel,
        'preguntas'       => $exam['preguntas'],
        'is_active'       => true,
    ]);

    return view('exams.preview', [
        'exam'    => $exam,
        'input'   => $data,
        'exam_id' => $savedExam->id,
    ]);
}


    /**
     * Mostrar la vista de monitoreo de examen
     */
    public function show()
    {
        return view('examen');
    }

    /**
     * Evaluar comportamiento del examen (mantiene DeepSeek por ahora)
     */
    public function evaluate(Request $request)
    {
        $metrics = $request->input('metrics', [
            'frames_procesados'  => 0,
            'rostros_detectados' => 0,
            'rostros_perdidos'   => 0,
            'desvios_mirada'     => 0,
            'duracion_segundos'  => 0,
        ]);

        $prompt = "Eres un sistema que evalúa comportamiento en exámenes.\n"
                . "Recibes métricas en formato JSON y debes responder en JSON con:\n"
                . " - score: número de 0 a 100 (riesgo de copia)\n"
                . " - nivel: bajo|medio|alto\n"
                . " - comentario: explicación breve en español.\n\n"
                . "Métricas:\n" . json_encode($metrics, JSON_PRETTY_PRINT);

        $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.deepseek.key'),
                'Content-Type'  => 'application/json',
            ])->post(config('services.deepseek.url'), [
                'model' => 'deepseek-chat',
                'messages' => [
                    [
                        'role'    => 'system',
                        'content' => 'Eres un asistente que responde SIEMPRE en JSON válido.',
                    ],
                    [
                        'role'    => 'user',
                        'content' => $prompt,
                    ],
                ],
                'stream' => false,
            ]);

        if ($response->failed()) {
            return response()->json([
                'success' => false,
                'message' => 'Error llamando a DeepSeek',
                'error'   => $response->json(),
            ], 500);
        }

        $content = $response->json()['choices'][0]['message']['content'] ?? '{}';
        $evaluation = json_decode($content, true);

        if ($evaluation === null) {
            $evaluation = [
                'score'      => null,
                'nivel'      => null,
                'comentario' => $content,
            ];
        }

        return response()->json([
            'success'    => true,
            'metrics'    => $metrics,
            'evaluation' => $evaluation,
        ]);
    }

    public function publish(Exam $exam)
    {
        // Solo el prof dueño
        if ($exam->teacher_id !== auth()->id()) {
            abort(403);
        }

        if (!$exam->course_id) {
            return back()->withErrors(['course_id' => 'Este examen no tiene curso asignado.']);
        }

        // Apagar otros exámenes del mismo curso
        Exam::where('course_id', $exam->course_id)->update(['is_active' => false]);

        // Encender este
        $exam->update(['is_active' => true]);

        return redirect()->route('profesor.dashboard')->with('success', 'Examen publicado para el curso.');
    }

    public function take(Course $course)
    {
        $user = auth()->user();

        if (!$user->courses()->whereKey($course->id)->exists()) {
            abort(403, 'No estás inscrito en este curso.');
        }

        $exam = Exam::where('course_id', $course->id)
            ->where('is_active', true)
            ->latest()
            ->first();

        if (!$exam) {
            return view('prueba', compact('course', 'exam'));
        }

        // ✅ SOLO 1 INTENTO
        $already = ExamResult::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($already) {
            return redirect()
                ->route('courses.examen.result', $course) // la creamos abajo
                ->with('info', 'Ya rendiste este examen.');
        }

        // ✅ session id para monitoreo (para WS y auditoría)
        $sessionId = 'exam_'.$exam->id.'_user_'.$user->id.'_'.now()->timestamp;

        return view('prueba', compact('course', 'exam', 'sessionId'));
    }

    public function submit(Request $request, Course $course)
    {
        $user = auth()->user();

        $exam = Exam::where('course_id', $course->id)
            ->where('is_active', true)
            ->latest()
            ->first();

        if (!$exam) {
            return redirect()->route('estudiante.dashboard')->with('error', 'No hay examen activo.');
        }

        // ✅ bloquear doble intento
        $already = ExamResult::where('exam_id', $exam->id)->where('user_id', $user->id)->exists();
        if ($already) {
            return redirect()->route('courses.examen.result', $course)->with('info', 'Ya rendiste este examen.');
        }

        // Validate: answers are required UNLESS exam was force-terminated
        $isTerminated = $request->input('terminated') == "1";
        
        $data = $request->validate([
            'answers' => $isTerminated ? 'nullable|array' : 'required|array',
            'proctoring_metrics' => 'nullable|string',
        ]);


        $metrics = json_decode($request->proctoring_metrics, true) ?: [];
        $warnings = (int)($metrics['warnings'] ?? 0);
        
        // Ensure answers is always an array (could be null if terminated)
        $data['answers'] = $data['answers'] ?? [];
        
        // CHECK: Forced close via JS flag OR input hidden field
        $forcedClose = (bool)($metrics['forced_close'] ?? false) || ($request->input('terminated') == "1");

        // ✅ Si forcedClose o 5 warnings => nota 0 y flagged
        if ($forcedClose || $warnings >= 5) {
            ExamResult::create([
                'exam_id'            => $exam->id,
                'user_id'            => $user->id,
                'score_obtained'     => 0,
                'score_max'          => $exam->score_max,
                'percentage'         => 0,
                'proctoring_metrics' => $metrics,
                'evaluation'         => ['answers' => $data['answers']],
                'status'             => 'flagged', // exam invalidated
            ]);

            return redirect()->route('estudiante.dashboard')
                ->with('error', 'Examen invalidado por exceder el límite de advertencias o cierre forzoso. Puedes ver el detalle en tus resultados.');
        }

        // ===== cálculo normal =====
        $answers = $data['answers'];
        $questions = $exam->preguntas ?? [];

        $scoreObtained = 0;
        foreach ($questions as $index => $question) {
            if (isset($answers[$index], $question['correcta']) && (int)$answers[$index] === (int)$question['correcta']) {
                $scoreObtained += (int)($question['puntaje'] ?? 1);
            }
        }

        $scoreMax = $exam->score_max;
        $percentage = $scoreMax > 0 ? round(($scoreObtained / $scoreMax) * 100, 2) : 0;

        // ✅ “flagged” por umbrales (mejor que solo rostros perdidos)
        $ui = $metrics['ui'] ?? [];
        $lm = $metrics['last_metrics'] ?? [];
        $duration = max(1, (int)($ui['duration_sec'] ?? 1));
        $minutes = max(1, $duration / 60);

        $desvios = (int)($lm['desvios_mirada'] ?? 0);
        $desviosPorMin = $desvios / $minutes;

        $flagged =
            ($ui['tab_hidden_count'] ?? 0) >= 2 ||
            ($ui['blur_count'] ?? 0) >= 2 ||
            ($lm['rostros_perdidos'] ?? 0) >= 6 ||
            ($desviosPorMin >= 80);

        $status = $flagged ? 'flagged' : 'completed';

        ExamResult::create([
            'exam_id'            => $exam->id,
            'user_id'            => $user->id,
            'score_obtained'     => $scoreObtained,
            'score_max'          => $scoreMax,
            'percentage'         => $percentage,
            'proctoring_metrics' => $metrics,
            'evaluation'         => ['answers' => $answers],
            'status'             => $status,
        ]);

        return redirect()->route('courses.examen.result', $course)
            ->with('success', $flagged
                ? 'Examen enviado, pero se detectaron irregularidades.'
                : 'Examen enviado correctamente.');
    }


    public function result(Course $course)
    {
        $user = auth()->user();

        if (!$user->courses()->whereKey($course->id)->exists()) {
            abort(403);
        }

        $exam = Exam::where('course_id', $course->id)->latest()->first();

        if (!$exam) {
            return redirect()->route('estudiante.dashboard')->with('error', 'No hay examen.');
        }

        $result = ExamResult::where('exam_id', $exam->id)
            ->where('user_id', $user->id)
            ->latest()
            ->first();

        if (!$result) {
            return redirect()->route('courses.examen.take', $course)->with('info', 'Aún no has rendido el examen.');
        }

        return view('exams.result', compact('course', 'exam', 'result'));
    }

    public function createForCourse(Course $course)
    {
        // seguridad: que solo el prof dueño del curso pueda gestionar
        if ($course->teacher_id !== auth()->id()) abort(403);

        return view('exams.create', compact('course'));
    }


}

