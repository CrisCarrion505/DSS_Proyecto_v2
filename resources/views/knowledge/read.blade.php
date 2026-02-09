<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lectura de refuerzo</title>
<style>
    body{font-family:system-ui;background:#0b1220;color:#e5e7eb;margin:0;padding:24px}
    .wrap{max-width:980px;margin:0 auto}
    .card{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:16px;overflow:hidden}
    .head{padding:18px;background:linear-gradient(135deg,rgba(79,70,229,.55),rgba(124,58,237,.55))}
    .head h1{margin:0;font-size:20px}
    .meta{margin-top:8px;display:flex;gap:12px;flex-wrap:wrap;font-size:13px;opacity:.9}
    .body{padding:18px;line-height:1.8}
    .rules{padding:14px;border-radius:14px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);margin:14px 0}
    .btn{border:0;padding:10px 14px;border-radius:12px;font-weight:800;cursor:pointer}
    .btn-green{background:#10b981;color:#062014}
    .btn-gray{background:#334155;color:#e5e7eb}
    .toast{position:fixed;right:18px;bottom:18px;background:rgba(15,23,42,.95);border:1px solid rgba(255,255,255,.14);padding:12px 14px;border-radius:14px;max-width:340px}
    .danger{color:#fb7185;font-weight:900}
</style>
</head>
<body>
<div class="wrap">

<div class="card">
    <div class="head">
        <h1>{{ $module->title }}</h1>
        <div class="meta">
            <span>Curso: {{ $module->course->course_id }} - {{ $module->course->name }}</span>
            <span>Tiempo estimado: {{ $module->estimated_minutes }} min</span>
        </div>
    </div>

    <div class="body">

        <div class="rules">
            <b>Antes de comenzar:</b>
            <ul>
                <li>Lee la guía con atención (te servirá para el examen).</li>
                <li>Permite cámara si se solicita (solo para métricas, NO se muestra en pantalla).</li>
                <li>Evita cambiar de pestaña repetidamente.</li>
            </ul>

            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:10px">
                <button class="btn btn-green" id="btnStart">Iniciar lectura</button>
                <button class="btn btn-gray" id="btnFinish" disabled>Finalizar</button>
            </div>
        </div>

        <h3>Lectura</h3>
        <div style="white-space:pre-wrap">{{ $module->content }}</div>

        <hr style="border-color:rgba(255,255,255,.12);margin:18px 0">

        <h3>Actividades (interactivas)</h3>

@if(!empty($module->activities))
    <div id="activities" style="margin-top:14px">
        <ol style="padding-left:18px">
           @foreach(($module->activities ?? []) as $idx => $a)
    @php
        $qid = "q_" . $idx;
        $type = $a['type'] ?? 'mcq';
        $options = $a['options'] ?? [];
        $answer = $a['answer'] ?? null; // ← campo correcto de tu prompt
    @endphp

    <li style="margin:16px 0; padding:14px; border-radius:14px; background:rgba(255,255,255,.06); border:1px solid rgba(255,255,255,.12);">
        <div style="font-weight:900; margin-bottom:10px">
            {{ $a['question'] ?? '' }}
        </div>

        {{-- MCQ --}}
        @if($type === 'mcq' && count($options) >= 2)
            <div style="display:flex; flex-direction:column; gap:8px">
                    @foreach($options as $opIndex => $opText)
                        <label style="display:flex;gap:10px;align-items:flex-start;cursor:pointer">
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
                    <input type="text" name="{{ $qid }}" placeholder="Escribe tu respuesta..."
                        style="width:100%; padding:10px; border-radius:12px; border:1px solid rgba(255,255,255,.2); background:rgba(255,255,255,.05); color:#e5e7eb; font-size:14px;" />
                </div>
            @endif

            {{-- FEEDBACK --}}
            <div id="{{ $qid }}_feedback" style="margin-top:10px; font-size:13px; display:none;"></div>
        </li>
    @endforeach

        </ol>

        <div style="display:flex; gap:10px; flex-wrap:wrap; margin-top:12px">
            <button class="btn btn-green" id="btnCheckActivities">Verificar respuestas</button>
            <button class="btn btn-gray" id="btnResetActivities" type="button">Reintentar</button>
        </div>

        <div id="activitiesSummary" style="margin-top:12px; display:none; padding:12px; border-radius:14px; background:rgba(16,185,129,.12); border:1px solid rgba(16,185,129,.25)">
            <b>Resultado:</b> <span id="activitiesScore"></span>
        </div>
    </div>
@else
    <p style="opacity:.8">Este módulo no tiene actividades aún.</p>
@endif

    </div>
</div>

<div id="toast" class="toast" style="display:none"></div>

<!-- elementos ocultos para cámara -->
<video id="video" autoplay muted playsinline style="display:none"></video>
<canvas id="canvas" width="480" height="360" style="display:none"></canvas>

<script>
/* ========= CONFIG ========= */
const sessionId = {{ $session->id }};
const heartbeatUrl = "{{ route('knowledge.heartbeat', $session) }}";
const finishUrl = "{{ route('knowledge.finish', $session) }}";
const dashboardUrl = "{{ route('estudiante.dashboard') }}";
const CSRF = "{{ csrf_token() }}";

// Si tu WS no está en localhost para el cliente, cambia esto:
const WS_URL = `wss://reconocimiento-1.onrender.com/ws/examen/knowledge_${sessionId}`;

/* ========= UI ========= */
const btnStart = document.getElementById('btnStart');
const btnFinish = document.getElementById('btnFinish');
const toast = document.getElementById('toast');
const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const ctx = canvas.getContext('2d');

/* ========= ESTADO ========= */
let ws = null;
let stream = null;
let sendInterval = null;
let heartbeatInterval = null;

const THRESHOLDS = {
  toastCooldownMs: 12000,

  // NEW: Direct thresholds for restart
  maxTabSwitches: 10,
  maxGazeDeviations: 600,
  maxFaceLosses: 50,

  // Soft nudges (informational only)
  faceLostSecondsToNudge: 8,
  gazeDeltaWindowSec: 20,
  gazeEventsToNudge: 6,
};

let lastToastAt = 0;

let lastWsMetrics = null;
let faceLostSince = null;

// Track totals from WebSocket
let totalFaceLosses = 0;
let totalGazeDeviations = 0;

let gazeWindowStart = Date.now();
let gazeSpikeCountInWindow = 0;

/* ========= PROCTORING ========= */
let proctoring = {
  ui: {
    tab_hidden_count: 0,
    blur_count: 0,
    copy_count: 0,
    paste_count: 0,
    contextmenu_count: 0,
    started_at: Date.now(),
    duration_sec: 0,
  },
  attention: {
    total_face_losses: 0,
    total_gaze_deviations: 0,
    gaze_spike_count: 0,
    last_score: 100,
  },
  events: [],
  last_ws: null,
  history: [],
  activities: null,
};

/* ========= HELPERS ========= */
function showToast(msg, danger = false) {
  toast.style.display = 'block';
  toast.innerHTML = danger ? `<span class="danger">⚠ ${msg}</span>` : `ℹ️ ${msg}`;
  setTimeout(() => toast.style.display = 'none', 3500);
}

function softNudge(msg) {
  const now = Date.now();
  if (now - lastToastAt < THRESHOLDS.toastCooldownMs) return;
  lastToastAt = now;
  showToast(msg, false);
}

function logEvent(type, detail = {}) {
  proctoring.events.push({ t: Date.now(), type, detail });
  if (proctoring.events.length > 200) proctoring.events.shift();
}

function checkThresholds() {
  if (proctoring.ui.tab_hidden_count >= THRESHOLDS.maxTabSwitches) {
    restartReading(`Excediste ${THRESHOLDS.maxTabSwitches} cambios de pestaña.`);
    return;
  }
  if (totalGazeDeviations >= THRESHOLDS.maxGazeDeviations) {
    restartReading(`Excediste ${THRESHOLDS.maxGazeDeviations} desvíos de mirada.`);
    return;
  }
  if (totalFaceLosses >= THRESHOLDS.maxFaceLosses) {
    restartReading(`Excediste ${THRESHOLDS.maxFaceLosses} pérdidas de rostro.`);
    return;
  }
}

function stopAll() {
  if (sendInterval) clearInterval(sendInterval);
  if (heartbeatInterval) clearInterval(heartbeatInterval);
  sendInterval = null;
  heartbeatInterval = null;

  if (ws && ws.readyState === WebSocket.OPEN) ws.close();
  ws = null;

  if (stream) {
    stream.getTracks().forEach(t => t.stop());
    stream = null;
  }

  btnStart.disabled = false;
  btnFinish.disabled = true;
}

async function restartReading(msg) {
  stopAll();
  proctoring.ui.duration_sec = Math.floor((Date.now() - proctoring.ui.started_at) / 1000);

  // Save metrics before restart
  await fetch(finishUrl, {
    method: 'POST',
    headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify({ proctoring_metrics: proctoring, status: 'restarted' })
  }).catch(() => {});

  alert(`⚠️ REINICIANDO LECTURA\n\n${msg}\n\nLa página se recargará para que vuelvas a leer desde el inicio.`);
  window.location.reload();
}

/* ========= MONITOREO DE CONCENTRACIÓN (WS) ========= */
function handleWsMetrics(data) {
  proctoring.last_ws = data;

  if (proctoring.history.length < 200) {
    proctoring.history.push({ t: Date.now(), ...data });
  }

  // Track totals from WebSocket
  if (data.rostros_perdidos != null) {
    totalFaceLosses = data.rostros_perdidos;
    proctoring.attention.total_face_losses = totalFaceLosses;
  }
  if (data.desvios_mirada != null) {
    totalGazeDeviations = data.desvios_mirada;
    proctoring.attention.total_gaze_deviations = totalGazeDeviations;
  }

  // Check thresholds after updating
  checkThresholds();

  // Soft nudges for face lost
  if (data.status === 'rostro_perdido') {
    if (!faceLostSince) faceLostSince = Date.now();

    const lostSec = (Date.now() - faceLostSince) / 1000;

    if (lostSec >= THRESHOLDS.faceLostSecondsToNudge) {
      logEvent('face_lost', { seconds: Math.round(lostSec) });
      softNudge(`No se detecta tu rostro. (${totalFaceLosses}/${THRESHOLDS.maxFaceLosses})`);
      faceLostSince = Date.now();
    }
  } else {
    faceLostSince = null;
  }

  // Gaze spike detection (soft nudge)
  if (lastWsMetrics && data.desvios_mirada != null) {
    const delta = (data.desvios_mirada - (lastWsMetrics.desvios_mirada ?? 0));
    if (delta >= 2) {
      gazeSpikeCountInWindow++;
      proctoring.attention.gaze_spike_count++;
    }
  }
  lastWsMetrics = data;

  const now = Date.now();
  const windowSec = (now - gazeWindowStart) / 1000;
  if (windowSec >= THRESHOLDS.gazeDeltaWindowSec) {
    if (gazeSpikeCountInWindow >= THRESHOLDS.gazeEventsToNudge) {
      logEvent('gaze_distract', { spikes: gazeSpikeCountInWindow, window_sec: Math.round(windowSec) });
      softNudge(`Parece que te estás distrayendo. (${totalGazeDeviations}/${THRESHOLDS.maxGazeDeviations})`);
    }
    gazeSpikeCountInWindow = 0;
    gazeWindowStart = now;
  }

  // Calculate attention score
  let score = 100;
  score -= Math.min(30, (proctoring.ui.tab_hidden_count ?? 0) * 5);
  score -= Math.min(30, (proctoring.ui.blur_count ?? 0) * 5);
  score -= Math.min(25, Math.floor(totalGazeDeviations / 20));
  score -= Math.min(25, Math.floor(totalFaceLosses * 2));

  score = Math.max(0, Math.min(100, score));
  proctoring.attention.last_score = score;
}

/* ========= START / FINISH ========= */
async function start() {
  btnStart.disabled = true;
  btnFinish.disabled = false;

  try {
    stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: false });
    video.srcObject = stream;
    showToast('Cámara activa. Iniciando monitoreo...');
  } catch (e) {
    addAlert('No se pudo acceder a la cámara');
  }

  try {
    ws = new WebSocket(WS_URL);

    ws.onopen = () => {
      sendInterval = setInterval(() => {
        if (!ws || ws.readyState !== WebSocket.OPEN) return;
        if (!stream) return;

        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        canvas.toBlob(blob => {
          if (!blob) return;
          blob.arrayBuffer().then(buffer => ws.send(buffer));
        }, 'image/jpeg', 0.7);
      }, 300);
    };

    ws.onmessage = (ev) => {
      try {
        const data = JSON.parse(ev.data);
        handleWsMetrics(data);
      } catch (e) {}
    };

    ws.onerror = () => addAlert('Error de conexión con monitoreo');
  } catch (e) {
    addAlert('No se pudo iniciar el WebSocket');
  }

  heartbeatInterval = setInterval(() => {
    proctoring.ui.duration_sec = Math.floor((Date.now() - proctoring.ui.started_at) / 1000);

    fetch(heartbeatUrl, {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
      body: JSON.stringify({ proctoring_metrics: proctoring, alert_count: alertCount })
    }).catch(()=>{});
  }, 5000);
}

function finish() {
  stopAll();
  proctoring.ui.duration_sec = Math.floor((Date.now() - proctoring.ui.started_at) / 1000);

  fetch(finishUrl, {
    method: 'POST',
    headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': CSRF },
    body: JSON.stringify({ proctoring_metrics: proctoring, status: 'completed' })
  }).finally(() => {
    window.location.href = dashboardUrl;
  });
}

/* ========= UI EVENTS ========= */
document.addEventListener('visibilitychange', () => {
  if (document.hidden) {
    proctoring.ui.tab_hidden_count++;
    logEvent('tab_switch', { count: proctoring.ui.tab_hidden_count });
    showToast(`Cambio de pestaña (${proctoring.ui.tab_hidden_count}/${THRESHOLDS.maxTabSwitches})`, true);
    checkThresholds();
  }
});

window.addEventListener('blur', () => {
  proctoring.ui.blur_count++;
  logEvent('blur', { count: proctoring.ui.blur_count });
});

document.addEventListener('copy', () => { proctoring.ui.copy_count++; });
document.addEventListener('paste', () => { proctoring.ui.paste_count++; });
document.addEventListener('contextmenu', () => { proctoring.ui.contextmenu_count++; });

btnStart.addEventListener('click', start);
btnFinish.addEventListener('click', finish);

/* ========= ACTIVIDADES ========= */
const activities = @json($module->activities ?? []);
const btnCheck = document.getElementById('btnCheckActivities');
const btnReset = document.getElementById('btnResetActivities');
const summaryBox = document.getElementById('activitiesSummary');
const scoreText = document.getElementById('activitiesScore');

function normalizeType(t) {
  if (!t) return 'mcq';
  if (t === 'true_false') return 'truefalse';
  return t;
}

function mcqExpectedToIndex(expected, options = []) {
  if (expected === null || expected === undefined) return null;

  // Si ya es número (0,1,2...) o string numérica
  if (!isNaN(expected)) return String(expected);

  const exp = String(expected).trim();

  // Caso 1: letra A/B/C/D
  if (/^[A-Za-z]$/.test(exp)) {
    return String(exp.toUpperCase().charCodeAt(0) - 65);
  }

  // Caso 2: "B. texto"
  const letterMatch = exp.match(/^([A-Za-z])\s*\./);
  if (letterMatch) {
    return String(letterMatch[1].toUpperCase().charCodeAt(0) - 65);
  }

  // Caso 3: texto exacto de opción
  const found = options.findIndex(o => String(o).trim().toLowerCase() === exp.toLowerCase());
  if (found >= 0) return String(found);

  return exp; // fallback
}

function normalizeExpected(q, type) {
  let expected = q.answer;
  if (expected === null || expected === undefined) return null;

  if (type === 'mcq') {
    expected = mcqExpectedToIndex(expected, q.options || []);
  } else if (type === 'truefalse' || type === 'true_false') {
    expected = String(expected).toLowerCase();
  } else {
    expected = String(expected);
  }
  return expected;
}

function prettyExpected(q, type, expected) {
  if (expected === null || expected === undefined) return '';
  if (type !== 'mcq') return String(expected);

  const idx = Number(expected);
  const opts = q.options || [];
  if (!Number.isNaN(idx) && opts[idx] !== undefined) {
    const letter = String.fromCharCode(65 + idx);
    return `${letter}. ${opts[idx]}`;
  }
  return String(expected);
}

function setFeedback(qIndex, ok, msg) {
  const box = document.getElementById(`q_${qIndex}_feedback`);
  if (!box) return;

  box.style.display = 'block';
  box.style.padding = '10px';
  box.style.borderRadius = '12px';
  box.style.border = ok ? '1px solid rgba(16,185,129,.35)' : '1px solid rgba(251,113,133,.35)';
  box.style.background = ok ? 'rgba(16,185,129,.10)' : 'rgba(251,113,133,.10)';
  box.style.color = ok ? '#34d399' : '#fb7185';
  box.innerHTML = ok ? `✅ ${msg}` : `❌ ${msg}`;
}

function clearFeedback() {
  for (let i = 0; i < activities.length; i++) {
    const box = document.getElementById(`q_${i}_feedback`);
    if (!box) continue;
    box.style.display = 'none';
    box.innerHTML = '';
  }
  if (summaryBox) summaryBox.style.display = 'none';
}

function resetAnswers() {
  for (let i = 0; i < activities.length; i++) {
    const qid = `q_${i}`;
    document.querySelectorAll(`input[name="${qid}"]`).forEach(x => x.checked = false);
    const input = document.querySelector(`input[name="${qid}"][type="text"]`);
    if (input) input.value = '';
  }
  clearFeedback();
  showToast('Respuestas reiniciadas.');
}

function getUserAnswer(i, type) {
  const qid = `q_${i}`;
  if (type === 'short_answer') {
    const input = document.querySelector(`input[name="${qid}"]`);
    return input ? input.value.trim() : '';
  }
  const el = document.querySelector(`input[name="${qid}"]:checked`);
  return el ? el.value : '';
}

function checkActivities() {
  let answered = 0;
  let correct = 0;
  const answersPayload = [];

  for (let i = 0; i < activities.length; i++) {
    const q = activities[i] || {};
    const type = normalizeType(q.type || 'mcq');

    const userAns = getUserAnswer(i, type);
    const expected = normalizeExpected(q, type);

    if (userAns !== '') answered++;

    // Guardar payload
    answersPayload.push({
      index: i,
      type,
      user_answer: userAns,
      expected_answer: expected
    });

    // Si no hay expected, solo registramos
    if (expected === null || expected === undefined) {
      if (userAns === '') setFeedback(i, false, 'Responde esta pregunta.');
      else setFeedback(i, true, 'Respuesta registrada.');
      continue;
    }

    // Comparación
    let ok = false;

    if (userAns === '') {
      ok = false;
    } else if (type === 'mcq') {
      ok = String(userAns) === String(expected);
    } else if (type === 'truefalse' || type === 'true_false') {
      ok = String(userAns).toLowerCase() === String(expected).toLowerCase();
    } else if (type === 'short_answer') {
      const u = String(userAns).toLowerCase().trim();
      const e = String(expected).toLowerCase().trim();
      // flexible pero sin falsos cuando uno es vacío
      ok = (u && e) ? (u === e || u.includes(e) || e.includes(u)) : false;
    }

    if (ok) {
      correct++;
      setFeedback(i, true, q.feedback || '¡Correcto!');
    } else {
      const expPretty = prettyExpected(q, type, expected);
      setFeedback(i, false, `Incorrecto. Esperado: <strong>${expPretty}</strong>`);
    }

    // Debug seguro (opcional): descomenta si quieres
    // console.log({ index: i, type, userAns, expected, rawAnswer: q.answer, options: q.options, ok });
  }

  const total = activities.length;
  const pct = total > 0 ? Math.round((correct / total) * 100) : 0;

  if (summaryBox) summaryBox.style.display = 'block';
  if (scoreText) scoreText.innerHTML = `<strong>${correct}/${total} correctas (${pct}%)</strong>`;

  proctoring.activities = { answered, correct, total, pct, answers: answersPayload };

  const msg = correct === total ? '🎉 ¡Perfecto!' : (correct > total * 0.7 ? '👍 Bien' : '📚 Sigue practicando');
  showToast(`${msg} ${correct}/${total} (${pct}%)`);
}

if (btnCheck) btnCheck.addEventListener('click', (e) => { e.preventDefault(); checkActivities(); });
if (btnReset) btnReset.addEventListener('click', (e) => { e.preventDefault(); resetAnswers(); });
</script>


</div>
</body>
</html>
