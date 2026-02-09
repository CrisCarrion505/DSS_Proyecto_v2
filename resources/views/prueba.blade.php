<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Examen - EduSecure</title>
    <style>
        :root {
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --danger: #ef4444;
            --success: #10b981;
            --warning: #f59e0b;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            padding: 20px;
            line-height: 1.6;
        }

        /* Layout Principal */
        .layout {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 340px; /* Monitor fijo a la derecha */
            gap: 24px;
            align-items: start;
        }

        @media (max-width: 900px) {
            .layout {
                grid-template-columns: 1fr; /* Apilado en móvil */
            }
            .monitor-panel {
                order: -1; /* Monitor arriba en móvil */
                position: static !important;
            }
        }

        /* Tarjetas */
        .card {
            background: var(--bg-card);
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        h1, h2, h3 { margin-top: 0; }
        h1 { font-size: 1.5rem; letter-spacing: -0.025em; }
        h2 { font-size: 1.25rem; color: var(--text-main); margin-bottom: 1rem; }
        
        .text-muted { color: var(--text-muted); font-size: 0.875rem; }
        
        /* Preguntas */
        .question {
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        .question:last-child { border-bottom: none; }
        
        .question-title {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 12px;
            display: block;
        }

        .option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 8px;
            cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
        }
        .option:hover {
            background: rgba(255,255,255,0.03);
            border-color: rgba(255,255,255,0.2);
        }
        .option input {
            accent-color: var(--accent);
            width: 18px;
            height: 18px;
        }

        /* Monitor Panel (Sticky en Desktop) */
        .monitor-panel {
            position: sticky;
            top: 20px;
        }

        .monitor-video {
            width: 100%;
            border-radius: 12px;
            background: #000;
            aspect-ratio: 4/3;
            transform: scaleX(-1);
            margin-bottom: 12px;
            object-fit: cover;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .status-ok { background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.2); }
        .status-warn { background: rgba(245, 158, 11, 0.1); color: var(--warning); border: 1px solid rgba(245, 158, 11, 0.2); }
        .status-error { background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); }

        .metric-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
            font-size: 0.8rem;
        }
        .metric-item {
            background: rgba(255,255,255,0.03);
            padding: 8px;
            border-radius: 8px;
            text-align: center;
        }
        .metric-val {
            display: block;
            font-weight: 700;
            font-size: 1.1rem;
        }
        .metric-label {
            color: var(--text-muted);
            font-size: 0.7rem;
            text-transform: uppercase;
        }

        /* Botones */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-primary { background: var(--accent); color: white; }
        .btn-primary:hover { background: var(--accent-hover); }
        .btn-secondary { background: rgba(255,255,255,0.05); color: var(--text-main); }
        .btn-secondary:hover { background: rgba(255,255,255,0.1); }
        .w-full { width: 100%; }

        /* Modal Reglas */
        .overlay {
            position: fixed; inset: 0; background: rgba(15, 23, 42, 0.9);
            display: flex; align-items: center; justify-content: center;
            z-index: 50; padding: 20px;
        }
        .modal {
            background: var(--bg-card);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            width: 100%; max-width: 500px;
            overflow: hidden;
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }

        .hidden { display: none !important; }
    </style>
</head>
<body>

@if(!isset($exam) || !$exam)
<div class="card" style="max-width:500px; margin: 40px auto; text-align:center;">
    <h2>Sin examen activo</h2>
    <p class="text-muted">No hay un examen disponible en este momento.</p>
    <a href="{{ route('estudiante.dashboard') }}" class="btn btn-secondary" style="margin-top:20px;">Volver al Dashboard</a>
</div>
@else

<!-- Modal de Inicio y Comprobación -->
<div class="overlay" id="startModal">
    <div class="modal">
        <div style="padding: 24px;">
            <h2>📋 Antes de comenzar</h2>
            <div class="text-muted" style="margin-bottom: 20px;">
                Para garantizar la integridad del examen, necesitamos verificar tu cámara y permisos.
            </div>

            <ul style="padding-left: 20px; margin-bottom: 24px; font-size: 0.9rem; color: var(--text-muted);">
                <li>Mantén tu rostro visible en todo momento.</li>
                <li>No cambies de pestaña ni minimices el navegador.</li>
                <li>Evita ruidos o personas adicionales en la sala.</li>
            </ul>

            <div style="background: rgba(0,0,0,0.3); border-radius: 12px; padding: 12px; margin-bottom: 20px; text-align: center;">
                <video id="previewVideo" autoplay muted playsinline style="width:100%; max-height: 200px; border-radius: 8px; display:none; transform: scaleX(-1);"></video>
                <div id="cameraPrompt" style="padding: 20px;">
                    <p style="margin-bottom:10px;">Haz clic para activar la cámara</p>
                    <button class="btn btn-secondary" id="btnCheckCamera">📷 Activar Cámara</button>
                </div>
            </div>

            <div style="display: flex; gap: 12px; justify-content: flex-end;">
                <a href="{{ route('estudiante.dashboard') }}" class="btn btn-secondary">Cancelar</a>
                <button class="btn btn-primary" id="btnStartExam" disabled>Iniciar Examen</button>
            </div>
        </div>
    </div>
</div>

<div class="layout">
    
    <!-- Columna Izquierda: Preguntas -->
    <main class="content-panel">
        <div class="card">
            <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom: 24px;">
                <div>
                    <h1>{{ $exam->titulo }}</h1>
                    <div class="text-muted">{{ $course->name }} &bull; {{ count($exam->preguntas ?? []) }} Preguntas</div>
                </div>
                <div class="status-badge status-ok" id="examStatusBadge">En Progreso</div>
            </div>

            <form method="POST" action="{{ route('courses.examen.submit', $course) }}" id="examForm">
                @csrf
                <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                <input type="hidden" name="proctoring_metrics" id="proctoring_metrics" value="{}">
                <input type="hidden" name="terminated" id="terminated" value="0">
                <input type="hidden" name="termination_reason" id="termination_reason" value="">

                <div id="questionsContainer" style="opacity: 0.1; pointer-events: none;"> <!-- Deshabilitado hasta iniciar -->
                    @foreach(($exam->preguntas ?? []) as $idx => $q)
                        <div class="question">
                            <span class="question-title">{{ $idx + 1 }}. {{ $q['texto'] ?? 'Pregunta sin texto' }} <span class="text-muted" style="font-weight:400; font-size: 0.8em; margin-left: 8px;">({{ $q['puntaje'] ?? 1 }} pts)</span></span>
                            
                            @foreach(($q['opciones'] ?? []) as $i => $opt)
                                <label class="option">
                                    <input type="radio" name="answers[{{ $idx }}]" value="{{ $i }}" required>
                                    <span>{{ $opt }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endforeach

                    <div style="margin-top: 32px; display: flex; justify-content: space-between;">
                         <a href="{{ route('estudiante.dashboard') }}" class="btn btn-secondary">Salir</a>
                        <button type="submit" class="btn btn-primary" id="btnSubmit">Finalizar y Enviar</button>
                    </div>
                </div>
            </form>
        </div>
    </main>

    <!-- Columna Derecha: Monitor -->
    <aside class="monitor-panel">
        <div class="card">
            <h3 style="margin-bottom:12px; font-size: 1rem;">Monitoreo en Vivo</h3>
            
            <video id="monitorVideo" autoplay muted playsinline class="monitor-video"></video>
            <canvas id="hiddenCanvas" width="480" height="360" style="display:none;"></canvas>

            <div class="metric-grid">
                <div class="metric-item">
                    <span class="metric-val" id="lblFaceLost">0</span>
                    <span class="metric-label">Rostros Perdidos</span>
                </div>
                <div class="metric-item">
                    <span class="metric-val" id="lblGaze">0</span>
                    <span class="metric-label">Desvíos Mirada</span>
                </div>
            </div>

            <div style="margin-top: 16px; padding: 12px; background: rgba(245, 158, 11, 0.1); border-radius: 8px; border: 1px solid rgba(245, 158, 11, 0.2);">
                <div style="font-size: 0.75rem; color: var(--warning); text-transform: uppercase; font-weight: 700; margin-bottom: 4px;">Advertencias</div>
                <div style="font-size: 1.5rem; font-weight: 800; color: var(--warning);" id="lblWarnings">0<span style="font-size:1rem; opacity:0.7">/5</span></div>
                <div style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.3; margin-top: 4px;">
                    Si alcanzas 5 advertencias, el examen se cerrará automáticamente.
                </div>
            </div>
            
            <div id="connectionStatus" style="font-size: 0.75rem; color: var(--text-muted); text-align: center; margin-top: 12px;">Desconectado</div>
        </div>
    </aside>

</div>

<script>
    const sessionId = "{{ $sessionId ?? 'debug_session' }}";
    const WS_URL = `wss://reconocimiento-1.onrender.com/ws/examen/${sessionId}`;
    
    // UI Elements
    const $startModal = document.getElementById('startModal');
    const $previewVideo = document.getElementById('previewVideo');
    const $cameraPrompt = document.getElementById('cameraPrompt');
    const $monitorVideo = document.getElementById('monitorVideo');
    const $btnCheck = document.getElementById('btnCheckCamera');
    const $btnStart = document.getElementById('btnStartExam');
    const $questions = document.getElementById('questionsContainer');
    const $examStatus = document.getElementById('examStatusBadge');
    
    // Metrics Elements
    const $lblFace = document.getElementById('lblFaceLost');
    const $lblGaze = document.getElementById('lblGaze');
    const $lblWarn = document.getElementById('lblWarnings');
    const $connStatus = document.getElementById('connectionStatus');
    
    // Logic State
    let stream = null;
    let ws = null;
    let intervalId = null;
    let isExamActive = false;
    
    // Config
    const MAX_WARNINGS = 5;
    const THRESHOLDS = {
        face_consecutive: 6, // frames
        gaze_total: 2000
    };
    
    let metrics = {
        warnings: 0,
        face_lost_counter: 0,
        tab_switches: 0,
        blur_counts: 0,
        history: [],
        start_time: null
    };

    // 1. Check Camera
    $btnCheck.addEventListener('click', async () => {
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { width: 480, height: 360 } });
            $previewVideo.srcObject = stream;
            $previewVideo.style.display = 'block';
            $cameraPrompt.style.display = 'none';
            $btnStart.disabled = false;
            $btnCheck.style.display = 'none';
        } catch (err) {
            alert('Error al acceder a la cámara. Por favor verifica los permisos.');
            console.error(err);
        }
    });

    // 2. Start Exam
    $btnStart.addEventListener('click', () => {
        if (!stream) return;
        
        // Move stream to monitor
        $monitorVideo.srcObject = stream;
        $previewVideo.srcObject = null;
        
        // Adjust UI
        $startModal.style.display = 'none';
        $questions.style.opacity = '1';
        $questions.style.pointerEvents = 'auto';
        
        // Init State
        isExamActive = true;
        metrics.start_time = Date.now();
        
        startProctoring();
    });

    // 3. WebSocket Logic
    function startProctoring() {
        $connStatus.textContent = 'Conectando...';
        ws = new WebSocket(WS_URL);
        
        ws.onopen = () => {
            $connStatus.textContent = '● Conectado y Monitoreando';
            $connStatus.style.color = 'var(--success)';
            
            const canvas = document.getElementById('hiddenCanvas');
            const ctx = canvas.getContext('2d');
            
            intervalId = setInterval(() => {
                if (ws.readyState === WebSocket.OPEN && isExamActive) {
                    ctx.drawImage($monitorVideo, 0, 0, 480, 360);
                    canvas.toBlob(blob => {
                        ws.send(blob);
                    }, 'image/jpeg', 0.6);
                }
            }, 500); // 2 FPS is enough for basic proctoring
        };
        
        ws.onmessage = (event) => {
            if (!isExamActive) return;
            try {
                const data = JSON.parse(event.data);
                handleServerData(data);
            } catch (e) {
                console.error("Data error", e);
            }
        };
        
        ws.onclose = () => {
            $connStatus.textContent = 'Desconectado';
            $connStatus.style.color = 'var(--text-muted)';
        };
        
        ws.onerror = (err) => {
            console.error("WS Error", err);
            $connStatus.textContent = 'Error de Conexión';
            $connStatus.style.color = 'var(--danger)';
        };
    }

    let localFaceConsecutive = 0;

    function handleServerData(data) {
        // Update UI
        $lblFace.innerText = data.rostros_perdidos ?? 0;
        $lblGaze.innerText = data.desvios_mirada ?? 0;

        // AUTH: Sync server metrics to global object for submission
        metrics.face_lost_counter = data.rostros_perdidos ?? metrics.face_lost_counter;
        metrics.gaze_deviations = data.desvios_mirada ?? 0;

        // Logic Check
        if (data.status === 'rostro_perdido') {
            localFaceConsecutive++;
        } else {
            localFaceConsecutive = 0;
        }

        if (localFaceConsecutive > THRESHOLDS.face_consecutive) {
            triggerWarning('No se detecta tu rostro. Mantente en cámara.');
            localFaceConsecutive = 0; // debounce
        }
        
        // Note: Gaze warnings handled by server aggregation usually, but we can check here
        if ((data.desvios_mirada ?? 0) > THRESHOLDS.gaze_total && metrics.warnings < 1) { // simple check
             // triggerWarning('Distracción visual detectada frecuentemente.');
        }
    }

    // 4. Client Side Events
    document.addEventListener("visibilitychange", () => {
        if (!isExamActive) return;
        if (document.hidden) {
            metrics.tab_switches++;
            triggerWarning('Has cambiado de pestaña. Esto está prohibido.');
        }
    });

    window.addEventListener("blur", () => {
        if (!isExamActive) return;
        metrics.blur_counts++;
        triggerWarning('Has perdido el foco de la ventana del examen.');
    });

    // 5. Warning System
    let lastWarningTime = 0;
    const WARNING_COOLDOWN_MS = 5000;

    function triggerWarning(msg) {
        const now = Date.now();
        if (now - lastWarningTime < WARNING_COOLDOWN_MS) {
            console.log("Warning ignored due to cooldown", msg);
            return;
        }

        lastWarningTime = now;
        
        metrics.warnings++;
        $lblWarn.innerText = `${metrics.warnings}/5`;
        
        alert(`⚠️ ADVERTENCIA #${metrics.warnings}\n${msg}`);

        // Reset buffer
        localFaceConsecutive = 0;
        
        if (metrics.warnings >= MAX_WARNINGS) {
            terminateExam('Límite de advertencias alcanzado.');
        }
    }

    function terminateExam(reason) {
        if (!isExamActive) return;
        isExamActive = false;
        
        // Stop Everything
        if (ws) ws.close();
        if (intervalId) clearInterval(intervalId);
        if (stream) stream.getTracks().forEach(track => track.stop());
        
        alert(`⛔ EXAMEN TERMINADO\nRazón: ${reason}`);
        
        
        document.getElementById('terminated').value = "1";
        document.getElementById('termination_reason').value = reason;
        
        // Finalize Data
        metrics.forced_close = true; // Flag for controller
        document.getElementById('proctoring_metrics').value = JSON.stringify(metrics);
        
        $examStatus.textContent = 'Terminado';
        $examStatus.className = 'status-badge status-error';

        // CLICK: Eliminar 'required' de todos los inputs para forzar envío
        const form = document.getElementById('examForm');
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.removeAttribute('required');
        });
        
        // Enviar formulario (submit normal)
        form.submit();
    }
    
    // Submit Handler
    document.getElementById('examForm').addEventListener('submit', () => {
        isExamActive = false; // Stop analytics
        document.getElementById('proctoring_metrics').value = JSON.stringify(metrics);
    });

</script>
@endif

</body>
</html>
