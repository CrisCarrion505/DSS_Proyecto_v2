<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Resultado del Examen</title>
  <style>
    :root {
        --bg-body: #0f172a;
        --bg-card: #1e293b;
        --text-main: #f8fafc;
        --text-muted: #94a3b8;
        --accent: #6366f1;
        --danger: #ef4444;
        --success: #10b981;
        --warning: #f59e0b;
    }
    body { font-family: 'Inter', system-ui, -apple-system, sans-serif; background: var(--bg-body); color: var(--text-main); margin:0; padding:24px; line-height: 1.5; }
    .wrap { max-width: 900px; margin: 0 auto; }
    
    .card { background: var(--bg-card); border-radius: 16px; overflow:hidden; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
    
    .head { padding: 32px; background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), rgba(168, 85, 247, 0.1)); border-bottom: 1px solid rgba(255,255,255,0.05); }
    .head h1 { margin:0; font-size: 1.8rem; letter-spacing: -0.025em; }
    .meta { margin-top: 12px; display:flex; gap:16px; flex-wrap:wrap; font-size: 0.9rem; color: var(--text-muted); }
    
    .body { padding: 32px; }
    
    .badge { display:inline-flex; align-items:center; padding: 4px 12px; border-radius: 9999px; font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.05em; }
    .badge-success { background: rgba(16, 185, 129, 0.1); color: var(--success); border: 1px solid rgba(16, 185, 129, 0.2); }
    .badge-danger { background: rgba(239, 68, 68, 0.1); color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); }
    
    .main-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; margin-bottom: 32px; }
    .stat-box { background: rgba(255,255,255,0.03); padding: 20px; border-radius: 12px; text-align: center; border: 1px solid rgba(255,255,255,0.05); }
    .stat-label { font-size: 0.8rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 8px; }
    .stat-value { font-size: 2rem; font-weight: 700; color: var(--text-main); }
    
    h3 { font-size: 1.1rem; margin-top: 0; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
    
    .metrics-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 16px; }
    .metric-card { background: rgba(0,0,0,0.2); padding: 16px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.05); }
    .metric-card.alert { border-color: rgba(239, 68, 68, 0.3); background: rgba(239, 68, 68, 0.05); }
    
    .m-label { font-size: 0.75rem; color: var(--text-muted); margin-bottom: 4px; }
    .m-value { font-size: 1.25rem; font-weight: 600; }
    
    .btn { display:inline-flex; align-items:center; justify-content:center; padding: 12px 20px; border-radius: 8px; font-weight: 600; text-decoration:none; transition: all 0.2s; }
    .btn-secondary { background: rgba(255,255,255,0.05); color: var(--text-main); }
    .btn-secondary:hover { background: rgba(255,255,255,0.1); }
    .btn-primary { background: var(--accent); color: white; margin-left: auto; }
    
    .actions { margin-top: 32px; display:flex; gap:12px; }
  </style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <div class="head">
      <div style="display:flex; justify-content:space-between; align-items:flex-start;">
        <div>
           <h1>{{ $exam->titulo ?? 'Examen' }}</h1>
           <div class="meta">
             <span>{{ $course->name }}</span> &bull; <span>Intento finalizado</span>
           </div>
        </div>
        <div>
            @if($result->status === 'flagged')
                <span class="badge badge-danger">Invalidado / Suspechoso</span>
            @else
                <span class="badge badge-success">Completado</span>
            @endif
        </div>
      </div>
    </div>

    <div class="body">
      
      @if($result->status === 'flagged')
      <div style="padding: 16px; background: rgba(239, 68, 68, 0.1); border-left: 4px solid var(--danger); border-radius: 4px; margin-bottom: 24px; color: #fca5a5;">
        <strong>Examen Anulado:</strong> Se detectaron múltiples infracciones o se excedió el límite de advertencias. La calificación se ha establecido en 0.
      </div>
      @endif

      <div class="main-stats">
        <div class="stat-box">
          <div class="stat-label">Calificación</div>
          <div class="stat-value" style="color: {{ $result->status === 'flagged' ? 'var(--danger)' : 'var(--accent)' }}">
            {{ $result->score_obtained }} <span style="font-size:1rem; opacity:0.6">/ {{ $result->score_max }}</span>
          </div>
        </div>
        <div class="stat-box">
            <div class="stat-label">Porcentaje</div>
            <div class="stat-value">{{ $result->percentage }}%</div>
        </div>
      </div>

      <hr style="border:0; border-top:1px solid rgba(255,255,255,0.05); margin: 32px 0;">

      <h3>🛡️ Reporte de Integridad (Proctoring)</h3>
      
      @php
        $m = $result->proctoring_metrics ?? [];
        $warnings = $m['warnings'] ?? 0;
        $faces = $m['face_lost_counter'] ?? 0;
        $gaze = $m['gaze_deviations'] ?? 0;
        $tabs = $m['tab_switches'] ?? 0;
        $blur = $m['blur_counts'] ?? 0;
      @endphp

      <div class="metrics-grid">
        <div class="metric-card {{ $warnings > 0 ? 'alert' : '' }}">
            <div class="m-label">Advertencias Totales</div>
            <div class="m-value">{{ $warnings }}</div>
        </div>
        <div class="metric-card {{ $faces > 5 ? 'alert' : '' }}">
            <div class="m-label">Rostros Perdidos</div>
            <div class="m-value">{{ $faces }}</div>
        </div>
        <div class="metric-card {{ $gaze > 1000 ? 'alert' : '' }}">
            <div class="m-label">Desvíos de Mirada</div>
            <div class="m-value">{{ $gaze }}</div>
        </div>
        <div class="metric-card {{ $tabs > 0 ? 'alert' : '' }}">
            <div class="m-label">Cambios de Pestaña</div>
            <div class="m-value">{{ $tabs }}</div>
        </div>
         <div class="metric-card {{ $blur > 0 ? 'alert' : '' }}">
            <div class="m-label">Pérdida de Foco (Blur)</div>
            <div class="m-value">{{ $blur }}</div>
        </div>
      </div>

      <div class="actions">
        <a class="btn btn-secondary" href="{{ route('estudiante.dashboard') }}">Volver al Dashboard</a>
        <a class="btn btn-secondary" href="{{ route('courses.show', $course) }}" style="margin-left: auto;">Ver Curso</a>
      </div>

    </div>
  </div>
</div>
</body>
</html>
