<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\KnowledgeModule;
use App\Models\KnowledgeSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class KnowledgeModuleController extends Controller
{
    // ===== PROFESOR =====

    public function create()
    {
        $courses = Course::where('teacher_id', auth()->id())->latest()->get();

        return view('knowledge.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'topic' => 'required|string|max:255',
            'pedagogy_model' => 'required|in:arcs_keller,gagne_9_events,blooms_taxonomy,constructivism_scaffold,spaced_retrieval',
            'estimated_minutes' => 'nullable|integer|min:5|max:60',
        ]);

        $course = Course::where('id', $data['course_id'])
            ->where('teacher_id', auth()->id())
            ->firstOrFail();

        $estimated = $data['estimated_minutes'] ?? 10;

        $modelText = match ($data['pedagogy_model']) {
            'arcs_keller' => "Modelo ARCS de Keller (Atención, Relevancia, Confianza, Satisfacción).",
            'gagne_9_events' => "Los 9 eventos de instrucción de Gagné.",
            'blooms_taxonomy' => "Taxonomía de Bloom (recordar→comprender→aplicar→analizar→evaluar→crear).",
            'constructivism_scaffold' => "Constructivismo + andamiaje (scaffolding) con ejemplos guiados.",
            'spaced_retrieval' => "Práctica de recuperación + repetición espaciada (micro-quiz y recap).",
        };

        // Prompt: salida JSON estricta
        $prompt = <<<PROMPT
Genera un módulo de lectura de refuerzo académico para un estudiante universitario.
Curso: "{$course->course_id} - {$course->name}"
Tema: "{$data['topic']}"
Modelo pedagógico: {$modelText}
Duración estimada: {$estimated} minutos

Devuelve SOLO JSON válido con esta estructura:

{
  "title": "string",
  "summary": "string",
  "estimated_minutes": number,
  "key_concepts": ["..."],
  "content": "texto de lectura bien estructurado en párrafos, con subtítulos",
  "activities": [
    {
      "type": "mcq|short_answer|true_false",
      "question": "string",
      "options": ["A","B","C","D"], // solo si mcq
      "answer": "string|number|boolean",
      "feedback": "string (explicación breve)"
    }
  ]
}

Reglas:
- Debe ser claro, correcto y útil.
- Máximo 800-1200 palabras en content.
- Actividades: 6 a 10.
- El contenido no debe incluir “según el modelo” textual; aplícalo de forma natural.
PROMPT;

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=' . config('services.gemini.key'), [
                'contents' => [['parts' => [['text' => $prompt]]]],
                'generationConfig' => ['temperature' => 0.2],
            ]);

        if ($response->failed()) {
            return back()->withInput()->withErrors(['api' => 'Error con Gemini: '.$response->status()]);
        }

        $raw = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}';

        // Limpieza por si Gemini mete ```json
        $raw = trim($raw);
        $raw = preg_replace('/^```json|```$/m', '', $raw);

        $start = strpos($raw, '{');
        $end = strrpos($raw, '}');
        $json = ($start !== false && $end !== false) ? substr($raw, $start, $end - $start + 1) : $raw;

        $payload = json_decode($json, true);

        if (!is_array($payload) || empty($payload['content']) || empty($payload['title'])) {
            return back()->withInput()->withErrors(['api' => 'Gemini no devolvió JSON válido.']);
        }

        $module = KnowledgeModule::create([
            'course_id' => $course->id,
            'teacher_id' => auth()->id(),
            'topic' => $data['topic'],
            'pedagogy_model' => $data['pedagogy_model'],
            'title' => $payload['title'],
            'summary' => $payload['summary'] ?? null,
            'content' => $payload['content'],
            'key_concepts' => $payload['key_concepts'] ?? [],
            'activities' => $payload['activities'] ?? [],
            'estimated_minutes' => $payload['estimated_minutes'] ?? $estimated,
            'is_active' => true,
        ]);

        return redirect()->route('knowledge.preview', $module);
    }

    public function preview(KnowledgeModule $module)
    {
        // solo prof dueño
        abort_unless($module->teacher_id === auth()->id(), 403);

        return view('knowledge.preview', compact('module'));
    }

    public function toggle(KnowledgeModule $module)
    {
        abort_unless($module->teacher_id === auth()->id(), 403);

        $module->update(['is_active' => !$module->is_active]);

        return back()->with('success', 'Estado actualizado.');
    }

    // ===== ESTUDIANTE =====

    public function read(KnowledgeModule $module)
    {
        $user = auth()->user();

        // solo si está inscrito en el curso
        if (!$user->courses()->whereKey($module->course_id)->exists()) {
            abort(403, 'No estás inscrito en este curso.');
        }

        // sesión (crea o reutiliza la última "reading")
        $session = KnowledgeSession::firstOrCreate([
            'knowledge_module_id' => $module->id,
            'user_id' => $user->id,
            'status' => 'reading',
        ], [
            'started_at' => now(),
            'proctoring_metrics' => [
                'ui' => [
                    'tab_hidden_count' => 0,
                    'blur_count' => 0,
                    'copy_count' => 0,
                    'paste_count' => 0,
                    'contextmenu_count' => 0,
                    'started_at' => now()->timestamp * 1000,
                ],
                'last_ws' => null,
                'history' => [],
            ],
        ]);

        return view('knowledge.read', compact('module', 'session'));
    }

    // recibe métricas periódicas (JS) mientras lee
    public function heartbeat(Request $request, KnowledgeSession $session)
    {
        abort_unless($session->user_id === auth()->id(), 403);

        $data = $request->validate([
            'proctoring_metrics' => 'required|array',
            'alert_count' => 'nullable|integer|min:0',
        ]);

        $session->update([
            'proctoring_metrics' => $data['proctoring_metrics'],
            'alert_count' => $data['alert_count'] ?? $session->alert_count,
        ]);

        return response()->json(['ok' => true]);
    }

    public function finish(Request $request, KnowledgeSession $session)
    {
        abort_unless($session->user_id === auth()->id(), 403);

        \Log::info("Finish knowledge session {$session->id}", $request->all());

        $data = $request->validate([
            'answers' => 'nullable|array',
            'score' => 'nullable|integer|min:0|max:100',
            'proctoring_metrics' => 'nullable|array',
            'status' => 'nullable|in:completed,flagged,closed',
        ]);

        $started = $session->started_at ?? now();
        $ended = now();
        $duration = $ended->diffInSeconds($started);

        $session->update([
            'ended_at' => $ended,
            'duration_sec' => $duration,
            'answers' => $data['answers'] ?? $session->answers,
            'score' => $data['score'] ?? $session->score,
            'proctoring_metrics' => $data['proctoring_metrics'] ?? $session->proctoring_metrics,
            'status' => $data['status'] ?? 'completed',
        ]);

        return redirect()->route('estudiante.dashboard')
            ->with('success', 'Lectura finalizada. ¡Listo para el examen!');


    }

    public function index()
    {
        $user = auth()->user();

        // Cursos donde el estudiante está inscrito
        $courseIds = $user->courses()->pluck('courses.id');

        // Módulos activos de esos cursos
        $modules = KnowledgeModule::whereIn('course_id', $courseIds)
            ->where('is_active', true)
            ->latest()
            ->get();

        return view('knowledge.index', compact('modules'));
    }

    public function testModule()
    {
        $module = KnowledgeModule::create([
            'course_id' => 1, // tu course_id
            'teacher_id' => auth()->id(),
            'topic' => 'Prueba actividades',
            'title' => 'Módulo de prueba',
            'content' => 'Contenido de prueba...',
            'activities' => [
                [
                    'type' => 'mcq',
                    'question' => '¿Qué es HTML?',
                    'options' => ['Lenguaje programación', 'Markup', 'Base datos', 'Framework'],
                    'answer' => 1, // B
                    'feedback' => 'HTML es un lenguaje de marcado.'
                ],
                [
                    'type' => 'true_false',
                    'question' => 'JavaScript es un lenguaje de marcado.',
                    'answer' => false,
                    'feedback' => 'No, es lenguaje de programación.'
                ],
                [
                    'type' => 'short_answer',
                    'question' => '¿Qué significa CSS?',
                    'answer' => 'cascading style sheets',
                    'feedback' => 'Cascading Style Sheets.'
                ]
            ]
        ]);

        return redirect()->route('knowledge.preview', $module);
    }

    public function report(KnowledgeSession $session)
    {
        // Solo profesor del curso o el propio estudiante (si permitimos self-review)
        // Por ahora, solo profesor
        $course = $session->module->course;
        abort_unless($course->teacher_id === auth()->id(), 403);

        $metrics = $session->proctoring_metrics ?? [];
        
        // Extraer contadores
        // Nota: en mi JS puse 'final_gaze' y 'final_faces' dentro de proctoring_metrics al terminar flagged
        // O en 'current_gaze' / 'current_faces' en heartbeat.
        
        // Intentar buscar los valores finales
        $gaze = $metrics['final_gaze'] ?? ($metrics['current_gaze'] ?? ($metrics['attention']['gaze_spike_count'] ?? 0));
        $faces = $metrics['final_faces'] ?? ($metrics['current_faces'] ?? ($metrics['attention']['face_lost_sec'] ?? 0)); // Ojo: en JS puse contadores raw
        $tabs = $metrics['ui']['tab_hidden_count'] ?? 0;
        
        // Lógica de Determinación
        $verdict = 'Atención Adecuada';
        $verdictColor = 'text-green-500';
        $details = 'El estudiante mantuvo el foco en el contenido.';

        if ($session->status === 'flagged') {
            $verdict = 'Pérdida de Concentración (Reiniciado)';
            $verdictColor = 'text-red-600';
            $details = 'El sistema detectó demasiadas distracciones y la sesión fue reiniciada.';
        } elseif ($gaze > 100 || $faces > 70 || $tabs > 5) {
             $verdict = 'Atención Baja';
             $verdictColor = 'text-yellow-500';
             $details = 'Se detectaron frecuentes desvíos de mirada o cambios de pestaña, aunque no críticos.';
        }

        return view('knowledge.report', compact('session', 'metrics', 'verdict', 'verdictColor', 'details', 'gaze', 'faces', 'tabs'));
    }
}
