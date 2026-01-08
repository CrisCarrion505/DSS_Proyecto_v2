<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Exam;
class ExamController extends Controller
{
    /**
     * Mostrar formulario para crear examen (GET /exams/create)
     */
    public function create()
    {
        return view('exams.create');
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
            'course_id'       => 'nullable|exists:courses,id', // ✅ Opcional: vincular a curso
        ]);

        $nivel = $data['level'] ?? 'basico';
        $puntajePorPregunta = max(1, intdiv($data['score_max'], $data['questions_count']));

        $prompt = "Genera {$data['questions_count']} preguntas de '{$data['topic']}' en JSON. Nivel: {$nivel}. Cada pregunta: 'texto', 'opciones'(4), 'correcta'(0-3), 'puntaje':{$puntajePorPregunta}. Responde SOLO JSON válido:
{
  \"titulo\": \"Examen de {$data['topic']}\",
  \"score_max\": {$data['score_max']},
  \"preguntas\": [...]
}";

        // 4. Llamar a GEMINI API
        $response = Http::withHeaders([
                'Content-Type' => 'application/json',
            ])
            ->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash-exp:generateContent?key=' . config('services.gemini.key'), [
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
            return back()
                ->withInput()
                ->withErrors([
                    'api' => 'Error con Gemini. Código: ' . $response->status(),
                ]);
        }

        $content = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
        $content = trim(preg_replace('/``````|\n\s*``````json\s*/', '', $content));

        $start = strpos($content, '{');
        $end = strrpos($content, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $jsonContent = substr($content, $start, $end - $start + 1);
        } else {
            $jsonContent = $content;
        }

        $exam = json_decode($jsonContent, true);

        if ($exam === null || !isset($exam['preguntas']) || count($exam['preguntas']) === 0) {
            return back()
                ->withInput()
                ->withErrors([
                    'api' => 'Gemini no generó JSON válido.',
                ]);
        }

        // ✅ GUARDAR EN BASE DE DATOS
        $savedExam = Exam::create([
            'course_id'       => $data['course_id'] ?? null,
            'teacher_id'      => auth()->id(),
            'titulo'          => $exam['titulo'] ?? "Examen de {$data['topic']}",
            'description'     => $exam['description'] ?? null,
            'score_max'       => $exam['score_max'] ?? $data['score_max'],
            'questions_count' => $data['questions_count'],
            'topic'           => $data['topic'],
            'level'           => $nivel,
            'preguntas'       => $exam['preguntas'], // ✅ Guardar array como JSON
            'is_active'       => true,
        ]);

        // 10. Mostrar preview
        return view('exams.preview', [
            'exam'  => $exam,
            'input' => $data,
            'exam_id' => $savedExam->id, // ✅ Pasar ID para vincular
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
}
