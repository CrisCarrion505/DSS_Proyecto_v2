<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Monitor de Examen</title>
    
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    
    <style>
       * {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: linear-gradient(135deg, #667eea, #764ba2);
        min-height: 100vh;
        padding: 20px;
        text-align: center;
        color: #111827;
    }

    /* Título */
    h2 {
        color: white;
        font-size: 2.2rem;
        margin-bottom: 20px;
    }

    /* Video */
    #video {
        width: 100%;
        max-width: 640px;
        height: 360px;
        border-radius: 16px;
        background: #000;
        box-shadow: 0 15px 35px rgba(0,0,0,0.3);
        margin-bottom: 20px;
    }

    /* Botones */
    button {
        padding: 14px 30px;
        border: none;
        border-radius: 40px;
        font-size: 1rem;
        font-weight: 700;
        cursor: pointer;
        margin: 10px;
        text-transform: uppercase;
        transition: all 0.3s ease;
    }

    #btnStart {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        box-shadow: 0 8px 25px rgba(16,185,129,0.4);
    }

    #btnStop {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        box-shadow: 0 8px 25px rgba(239,68,68,0.4);
    }

    button:hover:not(:disabled) {
        transform: translateY(-3px);
    }

    button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        box-shadow: none;
    }

    /* Dashboard */
    #dashboard {
        margin-top: 30px;
        padding: 25px;
        background: linear-gradient(135deg, #1e293b, #334155);
        border-radius: 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        color: white;
    }

    /* Métricas */
    .metric {
        background: rgba(255,255,255,0.1);
        border-radius: 16px;
        padding: 15px;
        font-size: 1rem;
        font-weight: 600;
        position: relative;
    }

    /* Estados */
    .ok {
        color: #10b981;
        font-weight: 800;
    }

    .error {
        color: #ef4444;
        font-weight: 800;
    }

    /* Canvas oculto */
    canvas {
        display: none;
    }

    /* Responsive */
    @media (max-width: 768px) {
        #video {
            height: 280px;
        }

        h2 {
            font-size: 1.8rem;
        }
    }

    </style>
</head>
<body>
    <h2>🧪 Monitor de Examen</h2>

    <video id="video" width="480" height="360" autoplay muted></video><br>

    <button id="btnStart" onclick="iniciarExamen()">Comenzar</button>
    <button id="btnStop" onclick="detenerExamen()" disabled>Detener</button>

    <div id="dashboard">
        <div class="metric">Frames: <span id="frames">0</span></div>
        <div class="metric">Rostros detectados: <span id="rostros">0</span></div>
        <div class="metric">Rostros perdidos: <span id="perdidos">0</span></div>
        <div class="metric">Desvíos mirada: <span id="desvios">0</span></div>
        <div class="metric">Estado: <span id="estado" class="ok">Listo</span></div>
    </div>

    <canvas id="canvas" width="480" height="360" style="display:none;"></canvas>

    <script>
    let ws = null;
    let stream = null;
    let sendingInterval = null;
    let examenId = 'examen_' + Date.now();

    const video   = document.getElementById('video');
    const canvas  = document.getElementById('canvas');
    const ctx     = canvas.getContext('2d');

    const spanFrames   = document.getElementById('frames');
    const spanRostros  = document.getElementById('rostros');
    const spanPerdidos = document.getElementById('perdidos');
    const spanDesvios  = document.getElementById('desvios');
    const spanEstado   = document.getElementById('estado');
    const dashboard    = document.getElementById('dashboard');
    const btnStart     = document.getElementById('btnStart');
    const btnStop      = document.getElementById('btnStop');

    // Pedir acceso a la cámara al cargar la página
    navigator.mediaDevices.getUserMedia({ video: true })
        .then(s => { video.srcObject = s; stream = s; })
        .catch(err => { alert('No se pudo acceder a la cámara: ' + err); });

    function iniciarExamen() {
        // Ajusta el puerto al que realmente corre FastAPI
        const WS_BASE = "wss://reconocimiento-1.onrender.com";
        ws = new WebSocket(`${WS_BASE}/ws/examen/${examenId}`);
        ws.onopen = () => {
            console.log('WS conectado');
            btnStart.disabled = true;
            btnStop.disabled = false;
            dashboard.style.display = 'block';

            // Enviar frames cada 300 ms aprox.
            sendingInterval = setInterval(() => {
                if (ws.readyState !== WebSocket.OPEN) return;

                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                canvas.toBlob(blob => {
                    if (!blob) return;
                    blob.arrayBuffer().then(buffer => {
                        ws.send(buffer);
                    });
                }, 'image/jpeg', 0.7);
            }, 300);
        };

        ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            actualizarDashboard(data);
        };

        ws.onerror = (e) => {
            console.error('Error WS', e);
            spanEstado.textContent = 'Error WebSocket';
            spanEstado.className = 'error';
        };

        ws.onclose = () => {
            console.log('WS cerrado');
            detenerExamen();
        };
    }

    function detenerExamen() {
        if (sendingInterval) {
            clearInterval(sendingInterval);
            sendingInterval = null;
        }
        if (ws && ws.readyState === WebSocket.OPEN) {
            ws.close();
        }
        btnStart.disabled = false;
        btnStop.disabled = true;
        spanEstado.textContent = 'Detenido';
        spanEstado.className = 'error';
    }

    function actualizarDashboard(data) {
        spanFrames.textContent   = data.frames_procesados   ?? 0;
        spanRostros.textContent  = data.rostros_detectados  ?? 0;
        spanPerdidos.textContent = data.rostros_perdidos    ?? 0;
        spanDesvios.textContent  = data.desvios_mirada      ?? 0;

        if (data.status === 'ok') {
            spanEstado.textContent = 'Monitoreando';
            spanEstado.className = 'ok';
        } else {
            spanEstado.textContent = 'Rostro perdido';
            spanEstado.className = 'error';
        }
    }
      // ✅ 1) Enviar al padre (tu vista prueba.blade.php)
    window.parent.postMessage(
      { type: "proctoring_metrics", payload: data },
      window.location.origin
    );
    window.addEventListener("message", (event) => {
  if (event.origin !== window.location.origin) return;
  if (!event.data || event.data.type !== "proctoring_command") return;

  if (event.data.action === "start") startWS();
  if (event.data.action === "stop")  stopWS();
});

    </script>
</body>
</html>
