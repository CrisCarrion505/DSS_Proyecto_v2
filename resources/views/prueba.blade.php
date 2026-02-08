<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Rendir Examen - EduSecure</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial; background: #0b1220; color: #e5e7eb; margin: 0; padding: 24px; }
        .wrap { max-width: 980px; margin: 0 auto; }
        .card { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.12); border-radius: 16px; overflow: hidden; }
        .head { padding: 22px 22px; background: linear-gradient(135deg, rgba(79,70,229,.55), rgba(124,58,237,.55)); }
        .head h1 { margin: 0; font-size: 22px; }
        .meta { margin-top: 8px; display: flex; gap: 14px; flex-wrap: wrap; font-size: 14px; opacity: 0.95; }
        .body { padding: 22px; }
        .q { padding: 18px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.12); margin-bottom: 14px; background: rgba(0,0,0,0.25); }
        .q h3 { margin: 0 0 10px 0; font-size: 16px; }
        .opt { display: flex; gap: 10px; align-items: flex-start; padding: 10px 12px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.12); margin: 8px 0; cursor: pointer; }
        .opt input { margin-top: 3px; }
        .actions { display: flex; justify-content: space-between; gap: 12px; margin-top: 18px; flex-wrap: wrap; }
        .btn { border: 0; padding: 12px 16px; border-radius: 12px; font-weight: 700; cursor: pointer; }
        .btn-primary { background: #10b981; color: #062014; }
        .btn-secondary { background: #334155; color: #e5e7eb; text-decoration: none; display: inline-flex; align-items: center; }
        .warn { padding: 16px; border-radius: 14px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.35); }
        .grid { display: grid; grid-template-columns: 1.2fr 1fr; gap: 14px; }
        .panel { padding: 14px; border-radius: 14px; border: 1px solid rgba(255,255,255,0.12); background: rgba(255,255,255,0.04); }
        .panel h2 { margin: 0 0 10px 0; font-size: 14px; opacity: .9; }
        .small { font-size: 12px; opacity: .8; }
        
        /* Video feeds */
        #video, #videoPreview { 
            width: 100%; 
            height: 360px; 
            border-radius: 14px; 
            background: #000; 
            object-fit: cover;
            transform: scaleX(-1); /* Espejo */
        }
        
        /* Barra de estado */
        .statusbar{
            display:flex; align-items:center; justify-content:space-between;
            gap:12px; padding:10px 12px; border-radius:14px;
            border:1px solid rgba(255,255,255,0.12);
            background: rgba(0,0,0,0.25);
            margin-bottom: 14px;
        }
        .pill{ padding:6px 10px; border-radius:999px; font-size:12px; font-weight:800; }
        .pill-ok{ background: rgba(16,185,129,.18); border:1px solid rgba(16,185,129,.35); }
        .pill-warn{ background: rgba(245,158,11,.16); border:1px solid rgba(245,158,11,.35); }
        .pill-bad{ background: rgba(239,68,68,.18); border:1px solid rgba(239,68,68,.35); }

        /* Modal reglas */
        .overlay{
            position:fixed; inset:0; background: rgba(0,0,0,.65);
            display:flex; align-items:center; justify-content:center;
            padding: 18px;
            z-index: 50;
        }
        .modal{
            width:min(860px, 100%);
            background: rgba(12,18,32,.95);
            border:1px solid rgba(255,255,255,0.12);
            border-radius: 16px;
            overflow:hidden;
        }
        .modal-head{ padding:16px 18px; background: rgba(79,70,229,.30); }
        .modal-body{ padding: 18px; display:grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .modal ul{ margin: 0; padding-left: 18px; }
        .modal .actions{ padding: 0 18px 18px 18px; justify-content: flex-end; }
        .hidden{ display:none !important; }

        @media(max-width: 900px){
            .grid{ grid-template-columns: 1fr; }
            .modal-body{ grid-template-columns: 1fr; }
            #video, #videoPreview { height: 280px; }
        }
    </style>
</head>
<body>
<div class="wrap">

@if(!isset($exam) || !$exam)
    <div class="card">
        <div class="head">
            <h1>Sin examen activo</h1>
            <div class="meta">
                <span>Curso: {{ $course->course_id }} — {{ $course->name }}</span>
            </div>
        </div>
        <div class="body">
            <div class="warn">No hay un examen publicado para este curso todavía.</div>
            <div class="actions">
                <a class="btn btn-secondary" href="{{ route('estudiante.dashboard') }}">← Volver</a>
            </div>
        </div>
    </div>
@else

    {{-- ✅ Modal de Reglas + Chequeo cámara --}}
    <div class="overlay" id="rulesOverlay">
        <div class="modal">
            <div class="modal-head">
                <h2 style="margin:0;font-size:16px;">Antes de iniciar</h2>
                <div class="small" style="margin-top:6px;">
                    Verifica que tu cámara funcione. El sistema registrará cambios de pestaña, pérdida de rostro y conducta anómala.
                </div>
            </div>

            <div class="modal-body">
                <div class="panel">
                    <h2>Reglas rápidas</h2>
                    <ul class="small" style="line-height:1.7;">
                        <li>No cambies de pestaña ni minimices.</li>
                        <li>Mantén tu rostro visible.</li>
                        <li>No uses copiar/pegar.</li>
                        <li>Si llegas a <b>5 advertencias</b>, el examen se cerrará y se registrará como inválido.</li>
                    </ul>
                </div>

                <div class="panel">
                    <h2>Chequeo de cámara</h2>
                    <div class="small">Si te ves en el video, estás listo.</div>
                    
                    {{-- VIDEO ELEMENT PREVIEW --}}
                    <video id="videoPreview" autoplay muted playsinline></video>
                </div>
            </div>

            <div class="actions">
                <button class="btn btn-secondary" type="button" onclick="location.href='{{ route('estudiante.dashboard') }}'">Cancelar</button>
                <button class="btn btn-primary" type="button" id="btnStartExam" disabled>Cargando cámara...</button>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="head">
            <h1>{{ $exam->titulo }}</h1>
            <div class="meta">
                <span>Curso: {{ $course->course_id }} — {{ $course->name }}</span>
                <span>Preguntas: {{ is_array($exam->preguntas) ? count($exam->preguntas) : 0 }}</span>
                <span>Máx: {{ $exam->score_max }} pts</span>
            </div>
        </div>

        <div class="body">
            <div class="statusbar">
                <div class="small">
                    Estado: <span id="txtState" class="pill pill-ok">Listo</span>
                    <span style="margin-left:10px;">Advertencias: <b id="txtWarnings">0</b>/5</span>
                </div>
                <div class="small" id="txtHint">Inicia el examen para comenzar el monitoreo.</div>
            </div>

            <div class="grid">
                <div class="panel">
                    <h2>Preguntas</h2>

                    <form method="POST" action="{{ route('courses.examen.submit', $course) }}" id="examForm">
                        @csrf
                        <input type="hidden" name="exam_id" value="{{ $exam->id }}">
                        <input type="hidden" name="proctoring_metrics" id="proctoring_metrics" value="{}">
                        <input type="hidden" name="terminated" id="terminated" value="0">
                        <input type="hidden" name="termination_reason" id="termination_reason" value="">

                        <div id="questionsWrap" class="hidden">
                            @foreach(($exam->preguntas ?? []) as $idx => $q)
                                <div class="q">
                                    <h3>
                                        {{ $idx + 1 }}. {{ $q['texto'] ?? '' }}
                                        <span style="opacity:.7">({{ $q['puntaje'] ?? 1 }} pts)</span>
                                    </h3>
                                    @foreach(($q['opciones'] ?? []) as $i => $opt)
                                        <label class="opt">
                                            <input type="radio" name="answers[{{ $idx }}]" value="{{ $i }}" required>
                                            <div><b>{{ chr(65 + $i) }}.</b> {{ $opt }}</div>
                                        </label>
                                    @endforeach
                                </div>
                            @endforeach

                            <div class="actions">
                                <a class="btn btn-secondary" href="{{ route('estudiante.dashboard') }}">← Volver</a>
                                <button type="submit" class="btn btn-primary" id="btnSubmit">Enviar examen</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="panel">
                    <h2>Monitoreo</h2>
                    <div class="small">
                        No se muestran métricas detalladas. El análisis corre en segundo plano.
                    </div>
                    
                    {{-- VIDEO ELEMENT REAL (Hidden from user view but active) --}}
                    {{-- Usaremos el mismo stream, pero aquí lo mostramos pequeño o lo dejamos visible --}}
                    <div style="margin-top:10px;">
                        <video id="video" autoplay muted playsinline></video>
                        <canvas id="canvas" style="display:none;"></canvas>
                    </div>
                    
                    <div class="small" style="margin-top:10px; opacity:.85;">
                        <span id="wsStatus" style="color:#aaa;">Desconectado</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script>
// ==========================================
// CONFIGURACIÓN (De examen.blade.php)
// ==========================================
const LIMIT_WARNINGS = 5;
const THRESHOLDS = {
  tab_hidden_warn: 1,        
  blur_warn: 2,              
  rostro_perdido_consec: 6,  
  desvio_total_warn: 2000,   
};

const COOLDOWN_MS = {
  tab: 8000, blur: 8000, rostro: 8000, desvio: 12000,
  copy: 12000, paste: 12000, contextmenu: 12000,
};

// ==========================================
// ESTADO GLOBAL
// ==========================================
let examStarted = false;
let warningCount = 0;
let lastWarnAt = {};
let rostroPerdidoConsec = 0;

// Objeto de reporte (Misma estructura que usaba tu iframe)
const proctoring = {
  ui: {
    tab_hidden_count: 0,
    blur_count: 0,
    copy_count: 0,
    paste_count: 0,
    contextmenu_count: 0,
    started_at: null,
    duration_sec: 0
  },
  last_metrics: null,
  metrics_history: [],
  warnings: [],      
  terminated: false, 
  termination_reason: null
};

// DOM
const $warnings = document.getElementById('txtWarnings');
const $state = document.getElementById('txtState');
const $hint = document.getElementById('txtHint');
const $wsStatus = document.getElementById('wsStatus');

const videoPreview = document.getElementById('videoPreview'); // En el modal
const video = document.getElementById('video');               // En el panel lateral
const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');
const btnStart = document.getElementById('btnStartExam');

// VARIABLES WEBSOCKET
let ws = null;
let stream = null;
let sendingInterval = null;
// Usamos el ID de sesión que trae el controlador
const SESSION_ID = "{{ $sessionId ?? 'debug_session_' . time() }}"; 
const WS_URL = `wss://reconocimiento-1.onrender.com/ws/examen/${SESSION_ID}`;

// ==========================================
// 1. INICIALIZAR CÁMARA
// ==========================================
// Pedimos acceso inmediatamente al cargar la página
navigator.mediaDevices.getUserMedia({ video: true })
    .then(s => {
        stream = s;
        videoPreview.srcObject = stream;
        video.srcObject = stream; // Lo ponemos también en el video del examen
        
        btnStart.disabled = false;
        btnStart.textContent = "Iniciar examen";
        console.log("Cámara iniciada correctamente.");
    })
    .catch(err => {
        console.error("Error cámara:", err);
        alert("No se pudo acceder a la cámara. Por favor verifica los permisos.");
        btnStart.textContent = "Error de cámara";
    });


// ==========================================
// 2. LÓGICA WEBSOCKET (Start/Stop)
// ==========================================
function startProctoring() {
    if (ws) return;

    console.log("Conectando WS:", WS_URL);
    $wsStatus.textContent = "Conectando...";
    ws = new WebSocket(WS_URL);

    ws.onopen = () => {
        console.log("WS Conectado");
        $wsStatus.textContent = "Monitor Activo";
        $wsStatus.style.color = "#10b981";

        // Loop de envío de frames (Misma lógica que examen.blade.php)
        sendingInterval = setInterval(() => {
            if (ws.readyState !== WebSocket.OPEN) return;

            // Dibujar video en canvas
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

            // Enviar blob
            canvas.toBlob(blob => {
                if (blob) {
                    blob.arrayBuffer().then(buffer => ws.send(buffer));
                }
            }, 'image/jpeg', 0.6); // Calidad ajustada
        }, 300); // 300ms = ~3.3 fps
    };

    ws.onmessage = (event) => {
        try {
            const data = JSON.parse(event.data);
            handleMetricsReceived(data);
        } catch (e) {
            console.error("Error parseando métrica:", e);
        }
    };

    ws.onerror = (e) => {
        console.error("Error WS:", e);
        $wsStatus.textContent = "Error conexión";
        $wsStatus.style.color = "#ef4444";
    };

    ws.onclose = () => {
        console.log("WS Cerrado");
        $wsStatus.textContent = "Desconectado";
        stopProctoring();
    };
}

function stopProctoring() {
    if (sendingInterval) {
        clearInterval(sendingInterval);
        sendingInterval = null;
    }
    if (ws) {
        // Evitar bucle infinito si se cierra en onError
        const tempWs = ws;
        ws = null;
        tempWs.close();
    }
}

// ==========================================
// 3. PROCESAR RESULTADOS (Igual que examen.blade)
// ==========================================
function handleMetricsReceived(m) {
    // m = { status, desvios_mirada, rostros_perdidos, ... }
    proctoring.last_metrics = m;
    
    // Guardar historial
    proctoring.metrics_history.push({ t: Date.now(), ...m });
    if (proctoring.metrics_history.length > 250) proctoring.metrics_history.shift();

    // 1. Rostro Perdido
    if (m.status === 'rostro_perdido') {
        rostroPerdidoConsec++;
    } else {
        rostroPerdidoConsec = 0;
    }

    if (rostroPerdidoConsec >= THRESHOLDS.rostro_perdido_consec) {
        addWarning('rostro', 'No se detecta tu rostro. Mantente frente a la cámara.', { consec: rostroPerdidoConsec });
        rostroPerdidoConsec = 0; // Reset pa no spamear
    }

    // 2. Desvíos (Acumulado desde backend)
    const desvios = m.desvios_mirada ?? 0;
    if (desvios >= THRESHOLDS.desvio_total_warn) {
        addWarning('desvio', 'Se detectó un nivel anormal de desvío de mirada.', { desvios });
        // No reseteamos el acumulador global, solo el warning local tiene cooldown
    }
}

// ==========================================
// 4. FUNCIONES UI (Alertas, State)
// ==========================================
function setState(text, kind){
  $state.textContent = text;
  $state.className = 'pill ' + (kind === 'bad' ? 'pill-bad' : kind === 'warn' ? 'pill-warn' : 'pill-ok');
}

function canWarn(type){
  const now = Date.now();
  const cd = COOLDOWN_MS[type] ?? 8000;
  if (!lastWarnAt[type] || (now - lastWarnAt[type]) > cd){
    lastWarnAt[type] = now;
    return true;
  }
  return false;
}

function addWarning(type, message, payload = null){
  if (!examStarted) return;
  if (proctoring.terminated) return;
  if (!canWarn(type)) return;

  warningCount++;
  $warnings.textContent = warningCount;
  proctoring.warnings.push({ t: Date.now(), type, message, payload });

  setState('Advertencia', 'warn');
  $hint.textContent = message;
  
  // Usamos alert nativo como bloqueo simple
  alert(`⚠️ Advertencia (${warningCount}/${LIMIT_WARNINGS})\n${message}`);

  if (warningCount >= LIMIT_WARNINGS){
    terminateExam(`Se superó el límite de ${LIMIT_WARNINGS} advertencias`);
  }
}

function terminateExam(reason){
  if (proctoring.terminated) return;
  proctoring.terminated = true;
  proctoring.termination_reason = reason;

  setState('Examen cerrado', 'bad');
  $hint.textContent = reason;
  alert(`⛔ Examen cerrado\n${reason}`);

  stopProctoring(); // Detener cámara/ws

  // Llenar inputs y enviar
  document.getElementById('terminated').value = "1";
  document.getElementById('termination_reason').value = reason;
  document.getElementById('btnSubmit')?.click();
}

// EVENTOS NAVEGADOR (Pestañas, blur, copy)
document.addEventListener("visibilitychange", () => {
    if (!examStarted) return;
    if (document.hidden) {
        proctoring.ui.tab_hidden_count++;
        if (proctoring.ui.tab_hidden_count >= THRESHOLDS.tab_hidden_warn) {
            addWarning('tab', 'No cambies de pestaña.', { count: proctoring.ui.tab_hidden_count });
        }
    }
});

window.addEventListener("blur", () => {
    if (!examStarted) return;
    proctoring.ui.blur_count++;
    if (proctoring.ui.blur_count >= THRESHOLDS.blur_warn) {
        addWarning('blur', 'No cambies de ventana (foco perdido).', { count: proctoring.ui.blur_count });
    }
});

['copy', 'paste', 'contextmenu'].forEach(evt => {
    document.addEventListener(evt, (e) => {
        if (!examStarted) return;
        if (evt === 'contextmenu') e.preventDefault();
        proctoring.ui[evt + '_count']++;
        addWarning(evt, `Acción restringida: ${evt}`, { count: proctoring.ui[evt + '_count'] });
    });
});

// START
btnStart.addEventListener('click', () => {
    examStarted = true;
    proctoring.ui.started_at = Date.now();
    
    // UI Update
    setState('Monitoreando', 'ok');
    $hint.textContent = 'Responde con calma. Se registrarán eventos anómalos.';
    document.getElementById('questionsWrap').classList.remove('hidden');
    document.getElementById('rulesOverlay').classList.add('hidden');
    
    // Arrancar WebSocket
    startProctoring();
});

// SUBMIT
document.getElementById('examForm').addEventListener('submit', function (e) {
    if (proctoring.ui.started_at) {
        proctoring.ui.duration_sec = Math.round((Date.now() - proctoring.ui.started_at) / 1000);
    }
    
    const payload = {
        ...proctoring,
        warning_count: warningCount,
        thresholds: THRESHOLDS
    };
    document.getElementById('proctoring_metrics').value = JSON.stringify(payload);
    
    stopProctoring();
});

</script>

@endif
</div>
</body>
</html>
