<!DOCTYPE html>
<html lang="es" class="dark">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lectura de refuerzo</title>
@vite(['resources/css/app.css', 'resources/js/app.js'])
<style>
    body{font-family:system-ui;background:#0b1220;color:#e5e7eb;margin:0;padding:24px}
    .wrap{max-width:1200px;margin:0 auto}
    .grid-layout { display: grid; grid-template-columns: 1fr 320px; gap: 20px; }
    .card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:16px;overflow:hidden}
    .head{padding:18px;background:linear-gradient(135deg,rgba(79,70,229,.55),rgba(124,58,237,.55))}
    .head h1{margin:0;font-size:20px}
    .meta{margin-top:8px;display:flex;gap:12px;flex-wrap:wrap;font-size:13px;opacity:.9}
    .body{padding:18px;line-height:1.8}
    .rules{padding:14px;border-radius:14px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);margin:14px 0}
    
    .btn{border:0;padding:10px 14px;border-radius:12px;font-weight:800;cursor:pointer; transition: all .2s;}
    .btn:disabled{opacity:0.5;cursor:not-allowed;}
    .btn-green{background:#10b981;color:#062014}
    .btn-green:hover:not(:disabled){background:#34d399}
    .btn-gray{background:#334155;color:#e5e7eb}
    
    .toast{position:fixed;right:18px;bottom:18px;background:rgba(15,23,42,.95);border:1px solid rgba(255,255,255,.14);padding:12px 14px;border-radius:14px;max-width:340px;z-index:9999;}
    .danger{color:#fb7185;font-weight:900}
    
    /* Monitor styles */
    .monitor-panel { position: sticky; top: 20px; }
    #video { width: 100%; height: auto; border-radius: 12px; background: #000; transform: scaleX(-1); border: 1px solid rgba(255,255,255,0.1); }
    .status-pill { display: inline-block; padding: 4px 10px; border-radius: 999px; font-size: 12px; font-weight: bold; background: rgba(255,255,255,0.1); }
    .status-ok { background: rgba(16,185,129,0.2); color: #34d399; }
    .status-warn { background: rgba(245,158,11,0.2); color: #fbbf24; }
    
    @media (max-width: 900px) {
        .grid-layout { grid-template-columns: 1fr; }
        .monitor-panel { position: static; margin-bottom: 20px; }
    }
</style>
</head>
<body>
<div class="wrap">
    
    <div class="grid-layout">
        <!-- Contenido Principal -->
        <div class="card">
            <div class="head">
                <h1>{{ $module->title }}</h1>
                <div class="meta">
                    <span>Curso: {{ $module->course->course_id }} - {{ $module->course->name }}</span>
                    <span>⏱ {{ $module->estimated_minutes }} min</span>
                </div>
            </div>

            <div class="body">
                <div class="rules" id="rulesSection">
                    <b>Instrucciones:</b>
                    <ul style="margin-top:5px;padding-left:20px;font-size:14px;opacity:0.9">
                        <li>La cámara debe permanecer activa.</li>
                        <li><b>Manten tu atención en la lectura.</b></li>
                        <li>Si el sistema detecta demasiadas distracciones (mirada desviada o rostro no visible), la sesión se reiniciará automáticamente.</li>
                    </ul>

                    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px">
                        <button class="btn btn-green" id="btnStart">Iniciar lectura</button>
                        <button class="btn btn-gray" id="btnFinish" disabled>Finalizar</button>
                    </div>
                </div>

                <div id="contentSection" style="opacity:0.5; pointer-events:none; transition: opacity 0.3s;">
                    <h3>Lectura</h3>
                    <div style="white-space:pre-wrap; font-size:16px;">{{ $module->content }}</div>

                    <hr style="border-color:rgba(255,255,255,.12);margin:24px 0">

                    <h3>Actividades de Refuerzo</h3>
                    
                    @if(!empty($module->activities))
                        <div id="activities">
                            <ol style="padding-left:18px">
                               @foreach(($module->activities ?? []) as $idx => $a)
                                    @php
                                        $qid = "q_" . $idx;
                                        $type = $a['type'] ?? 'mcq';
                                        $options = $a['options'] ?? [];
                                    @endphp
    
                                    <li style="margin:20px 0; padding:16px; border-radius:14px; background:rgba(255,255,255,.03); border:1px solid rgba(255,255,255,.08);">
                                        <div style="font-weight:700; margin-bottom:12px">
                                            {{ $a['question'] ?? '' }}
                                        </div>
    
                                        {{-- MCQ --}}
                                        @if($type === 'mcq' && count($options) >= 2)
                                            <div style="display:flex; flex-direction:column; gap:8px">
                                                @foreach($options as $opIndex => $opText)
                                                    <label style="display:flex;gap:10px;align-items:flex-start;cursor:pointer;padding:8px;border-radius:8px;transition:background .2s" onmouseover="this.style.background='rgba(255,255,255,0.05)'" onmouseout="this.style.background='transparent'">
                                                        <input type="radio" name="{{ $qid }}" value="{{ $opIndex }}" />
                                                        <span><b>{{ chr(65 + $opIndex) }}.</b> {{ $opText }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @endif
    
                                        {{-- TRUE/FALSE --}}
                                        @if($type === 'true_false' || $type === 'truefalse')
                                            <div style="display:flex; gap:18px; flex-wrap:wrap">
                                                <label style="display:flex;gap:10px;align-items:center;cursor:pointer">
                                                    <input type="radio" name="{{ $qid }}" value="true" />
                                                    <span><b>Verdadero</b></span>
                                                </label>
                                                <label style="display:flex;gap:10px;align-items:center;cursor:pointer">
                                                    <input type="radio" name="{{ $qid }}" value="false" />
                                                    <span><b>Falso</b></span>
                                                </label>
                                            </div>
                                        @endif
    
                                        {{-- SHORT ANSWER --}}
                                        @if($type === 'short_answer')
                                            <div style="margin-top:8px">
                                                <input type="text" name="{{ $qid }}" placeholder="Tu respuesta..."
                                                    style="width:100%; padding:10px; border-radius:12px; border:1px solid rgba(255,255,255,.2); background:rgba(255,255,255,.05); color:#e5e7eb; font-size:14px;" />
                                            </div>
                                        @endif
    
                                        {{-- FEEDBACK --}}
                                        <div id="{{ $qid }}_feedback" style="margin-top:10px; font-size:13px; display:none;"></div>
                                    </li>
                                @endforeach
                            </ol>
    
                            <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:20px">
                                <button class="btn btn-green" id="btnCheckActivities">Verificar respuestas</button>
                                <button class="btn btn-gray" id="btnResetActivities" type="button">Reintentar</button>
                            </div>
    
                            <div id="activitiesSummary" style="margin-top:16px; display:none; padding:14px; border-radius:14px; background:rgba(16,185,129,.12); border:1px solid rgba(16,185,129,.25)">
                                <b>Resultado:</b> <span id="activitiesScore"></span>
                            </div>
                        </div>
                    @else
                        <p style="opacity:.6; font-style:italic;">Este módulo es solo lectura.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Panel Lateral (Monitoreo) -->
        <div class="monitor-panel">
            <div class="card">
                <div class="head" style="padding:14px; background: rgba(255,255,255,0.05);">
                    <h3 style="margin:0;font-size:14px; text-transform:uppercase; letter-spacing:1px; opacity:0.8;">Monitoreo</h3>
                </div>
                <div class="body" style="padding:14px;">
                    <video id="video" autoplay muted playsinline></video>
                    <canvas id="canvas" style="display:none;"></canvas>
                    
                    <div style="margin-top:12px; font-size:13px; opacity:0.8; display:flex; flex-direction:column; gap:5px;">
                        <div>Status: <span id="wsStatus" class="status-pill status-warn">Inactivo</span></div>
                        <div style="font-size:11px; opacity:0.5; margin-top:5px">
                            Rostros perdidos: <span id="debugFace">0</span> / 100<br>
                            Desvíos mirada: <span id="debugGaze">0</span> / 150
                        </div>
                    </div>

                    <div style="margin-top:14px; font-size:12px; color:#aaa; line-height:1.4;">
                        ℹ Tu cámara es visible para confirmar que la IA está funcionando.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="toast" class="toast" style="display:none"></div>

<script>
/* ========= CONFIG ========= */
const sessionId = {{ $session->id }};
const heartbeatUrl = "{{ route('knowledge.heartbeat', $session) }}";
const finishUrl = "{{ route('knowledge.finish', $session) }}";
const dashboardUrl = "{{ route('estudiante.dashboard') }}";
const CSRF = "{{ csrf_token() }}";

// WebSocket URL DIRECTA
const WS_URL = `wss://reconocimiento-1.onrender.com/ws/examen/knowledge_${sessionId}`;

/* ========= UI ========= */
const btnStart = document.getElementById('btnStart');
const btnFinish = document.getElementById('btnFinish');
const toast = document.getElementById('toast');
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');
const wsStatus = document.getElementById('wsStatus');
const contentSection = document.getElementById('contentSection');

const debugFace = document.getElementById('debugFace');
const debugGaze = document.getElementById('debugGaze');

/* ========= ESTADO ========= */
let ws = null;
let stream = null;
let sendInterval = null;
let heartbeatInterval = null;
let started = false;

/* ========= RESTART THRESHOLDS ========= */
const THRESHOLDS = {
    maxGazeDeviations: 150,
    maxLostFaces: 100
};

let currentGazeDeviations = 0;
let currentLostFaces = 0;

/* ========= HELPERS ========= */
function showToast(msg) {
  toast.style.display = 'block';
  toast.innerHTML = `ℹ️ ${msg}`;
  setTimeout(() => toast.style.display = 'none', 3000);
}

async function start() {
    if(started) return;
    
    // 1. Pedir Cámara
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
        video.srcObject = stream;
    } catch (e) {
        alert("Error: No se pudo acceder a la cámara. Es necesaria para la lectura.");
        return;
    }

    // 2. Conectar WS
    connectWs();

    // 3. UI Updates
    started = true;
    btnStart.disabled = true;
    btnFinish.disabled = false;
    contentSection.style.opacity = "1";
    contentSection.style.pointerEvents = "auto";
    
    // 4. Heartbeat Loop
    heartbeatInterval = setInterval(sendHeartbeat, 5000);
}

function connectWs() {
    wsStatus.textContent = "Conectando...";
    ws = new WebSocket(WS_URL);

    ws.onopen = () => {
        wsStatus.textContent = "Monitor Activo";
        wsStatus.className = "status-pill status-ok";
        
        // Loop de envío de frames
        sendInterval = setInterval(() => {
            if (!ws || ws.readyState !== WebSocket.OPEN) return;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            canvas.toBlob(blob => {
                if (blob) blob.arrayBuffer().then(buf => ws.send(buf));
            }, 'image/jpeg', 0.6);
        }, 300);
    };

    ws.onmessage = (ev) => {
        try {
            handleMetrics(JSON.parse(ev.data));
        } catch(e) {}
    };

    ws.onerror = () => {
        wsStatus.textContent = "Error";
        wsStatus.className = "status-pill status-warn";
    };
    
    ws.onclose = () => {
        if(started) {
            wsStatus.textContent = "Desconectado";
            wsStatus.className = "status-pill status-warn";
        }
    };
}

function handleMetrics(data) {
    // data = { status, desvios_mirada, rostros_perdidos, ... }
    
    // Actualizar contadores si vienen en la data
    if (data.desvios_mirada !== undefined) currentGazeDeviations = data.desvios_mirada;
    if (data.rostros_perdidos !== undefined) currentLostFaces = data.rostros_perdidos;
    
    // UI Debug
    if(debugFace) debugFace.textContent = currentLostFaces;
    if(debugGaze) debugGaze.textContent = currentGazeDeviations;

    // CHECK RESTART
    // Si se supera CUALQUIERA de los límites, reiniciamos.
    if (currentGazeDeviations > THRESHOLDS.maxGazeDeviations || 
        currentLostFaces > THRESHOLDS.maxLostFaces) {
            
        triggerRestart();
    }
}

function triggerRestart() {
    stopAll();
    
    fetch(finishUrl, {
         method: 'POST',
         headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
         body: JSON.stringify({ 
             proctoring_metrics: { 
                 final_gaze: currentGazeDeviations, 
                 final_faces: currentLostFaces 
             }, 
             status: 'flagged' 
         })
    }).finally(() => {
        alert("Se han detectado demasiadas distracciones (mirada o rostro). La lectura se reiniciará.");
        window.location.reload();
    });
}

function sendHeartbeat() {
    fetch(heartbeatUrl, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ 
            alert_count: 0, 
            proctoring_metrics: { current_gaze: currentGazeDeviations, current_faces: currentLostFaces }
        })
    }).catch(()=>{});
}

function stopAll() {
    if (sendInterval) clearInterval(sendInterval);
    if (heartbeatInterval) clearInterval(heartbeatInterval);
    if (stream) stream.getTracks().forEach(t => t.stop());
    if (ws) ws.close();
}

btnStart.addEventListener('click', start);
btnFinish.addEventListener('click', () => {
    stopAll();
    
    // Calcular duración final
    proctoring.ui.duration_sec = Math.floor((Date.now() - proctoring.ui.started_at) / 1000);

    fetch(finishUrl, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ 
            proctoring_metrics: proctoring,
            status: 'completed' 
        })
    })
    .then(response => {
        if (!response.ok) throw new Error('Error en respuesta del servidor');
        window.location.href = dashboardUrl;
    })
    .catch(error => {
        console.error(error);
        alert('Hubo un problema al guardar tu sesión. Por favor avisa al profesor.');
        // Opcional: permitir ir al dashboard de todos modos o reintentar
        window.location.href = dashboardUrl;
    });
});

/* ========= ACTIVIDADES ========= */
const activities = @json($module->activities ?? []);

function checkActivities() {
    let correct = 0;
    const total = activities.length;
    activities.forEach((q, i) => {
        const qid = `q_${i}`;
        const type = q.type || 'mcq';
        let userAns = '';
        if(type === 'mcq' || type === 'true_false' || type === 'truefalse') {
            const el = document.querySelector(`input[name="${qid}"]:checked`);
            if(el) userAns = el.value;
        } else {
            const el = document.querySelector(`input[name="${qid}"]`);
            if(el) userAns = el.value.trim();
        }
        
        // Validación simplificada visual
        const feedback = document.getElementById(`${qid}_feedback`);
        feedback.style.display = 'block';
        feedback.innerHTML = `<span style="color:#10b981">Respuesta guardada</span>`;
    });
    showToast('Respuestas verificadas');
}

const btnCheck = document.getElementById('btnCheckActivities');
const btnReset = document.getElementById('btnResetActivities');
if(btnCheck) btnCheck.addEventListener('click', checkActivities);
if(btnReset) btnReset.addEventListener('click', () => {
    document.querySelectorAll('input').forEach(i => {
        if(i.type==='radio') i.checked=false;
        if(i.type==='text') i.value='';
    });
    document.querySelectorAll('[id$="_feedback"]').forEach(e => e.style.display='none');
    showToast('Reiniciado');
});
</script>
</body>
</html>
