<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduSecure | Exámenes y clases vigiladas con visión por computadora</title>
  
  <link rel="icon" href="/favicon.ico" sizes="any">
  <link rel="icon" href="/favicon.svg" type="image/svg+xml">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">
  
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

  <style>
    :root{
      --brand:#6366f1;
      --brand2:#8b5cf6;
      --brand3:#a855f7;
      --text: rgba(255,255,255,.95);
      --text-muted: rgba(255,255,255,.65);
      --shadow: 0 25px 60px rgba(0,0,0,.25);
      --shadow-lg: 0 40px 80px rgba(0,0,0,.35);
    }

    *{ margin:0; padding:0; box-sizing:border-box; }
    html{ scroll-behavior:smooth; }

    body{
      font-family: "Inter", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
      color: var(--text);
      background: #0a0a0f;
      min-height: 100vh;
      overflow-x: hidden;
    }

    /* Animated gradient background */
    body::before {
      content: "";
      position: fixed;
      inset: 0;
      background: 
        radial-gradient(circle at 20% 20%, rgba(99,102,241,.15), transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(139,92,246,.12), transparent 50%),
        radial-gradient(circle at 40% 60%, rgba(168,85,247,.08), transparent 40%);
      animation: backgroundShift 20s ease infinite;
      z-index: -1;
    }

    @keyframes backgroundShift {
      0%, 100% { transform: scale(1) rotate(0deg); }
      50% { transform: scale(1.1) rotate(5deg); }
    }

    .wrap{ width:min(1240px, 92vw); margin-inline:auto; }

    /* Topbar */
    .topbar{
      position: sticky;
      top: 0;
      z-index: 50;
      backdrop-filter: blur(20px) saturate(180%);
      background: rgba(10,10,15,.75);
      border-bottom: 1px solid rgba(255,255,255,.06);
      animation: slideDown 0.6s ease;
    }

    @keyframes slideDown {
      from { transform: translateY(-100%); opacity: 0; }
      to { transform: translateY(0); opacity: 1; }
    }

    .topbar-inner{
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding: 16px 0;
      gap: 18px;
    }

    .brand{
      display:flex;
      align-items:center;
      gap:12px;
      font-weight: 900;
      letter-spacing:.5px;
      font-size: 1.1rem;
      animation: fadeIn 0.8s ease 0.2s both;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(10px); }
      to { opacity: 1; transform: translateY(0); }
    }

    .logo{
      width:40px;
      height:40px;
      border-radius: 12px;
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      box-shadow: 0 8px 24px rgba(99,102,241,.4), 0 0 0 1px rgba(255,255,255,.1);
      position:relative;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .logo:hover {
      transform: scale(1.05) rotate(-5deg);
      box-shadow: 0 12px 32px rgba(99,102,241,.5), 0 0 0 1px rgba(255,255,255,.15);
    }

    .logo::after{
      content:"";
      position:absolute;
      inset:10px;
      border-radius:8px;
      border:2.5px solid rgba(255,255,255,.7);
    }

    nav.menu{
      display:flex;
      gap: 8px;
      flex-wrap:wrap;
      align-items:center;
      justify-content:center;
    }

    nav.menu a{
      color: var(--text-muted);
      text-decoration:none;
      font-weight: 600;
      font-size: .95rem;
      padding: 10px 18px;
      border-radius: 12px;
      transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
    }

    nav.menu a::before {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(255,255,255,.08);
      border-radius: 12px;
      opacity: 0;
      transition: opacity 0.25s ease;
    }

    nav.menu a:hover{
      color: var(--text);
      transform: translateY(-2px);
    }

    nav.menu a:hover::before {
      opacity: 1;
    }

    .cta{
      display:flex;
      gap:12px;
      align-items:center;
      flex-wrap:wrap;
      justify-content:flex-end;
    }

    .btn{
      border:none;
      cursor:pointer;
      border-radius: 12px;
      padding: 12px 24px;
      font-weight: 700;
      letter-spacing:.3px;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      text-decoration:none;
      display:inline-flex;
      align-items:center;
      gap:10px;
      white-space:nowrap;
      font-size: .95rem;
      position: relative;
      overflow: hidden;
    }

    .btn::before {
      content: "";
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      border-radius: 50%;
      background: rgba(255,255,255,.2);
      transform: translate(-50%, -50%);
      transition: width 0.6s, height 0.6s;
    }

    .btn:hover::before {
      width: 300px;
      height: 300px;
    }

    .btn-primary{
      color:white;
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      box-shadow: 0 8px 24px rgba(99,102,241,.35), inset 0 1px 0 rgba(255,255,255,.2);
    }

    .btn-primary:hover{ 
      transform: translateY(-2px);
      box-shadow: 0 12px 32px rgba(99,102,241,.45), inset 0 1px 0 rgba(255,255,255,.3);
    }

    .btn-ghost{
      color: var(--text);
      background: rgba(255,255,255,.06);
      border: 1px solid rgba(255,255,255,.12);
      backdrop-filter: blur(10px);
    }

    .btn-ghost:hover{ 
      transform: translateY(-2px);
      background: rgba(255,255,255,.1);
      border-color: rgba(255,255,255,.2);
    }

    /* Hero */
    .hero{
      position:relative;
      padding: 100px 0 80px;
      overflow:hidden;
    }

    .hero::before {
      content: "";
      position: absolute;
      top: -50%;
      left: 50%;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle, rgba(99,102,241,.15), transparent 70%);
      transform: translateX(-50%);
      animation: pulse 8s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 0.5; transform: translateX(-50%) scale(1); }
      50% { opacity: 0.8; transform: translateX(-50%) scale(1.1); }
    }

    .hero-content{
      text-align: center;
      max-width: 900px;
      margin: 0 auto;
      position: relative;
      z-index: 1;
    }

    .pill{
      display:inline-flex;
      align-items:center;
      gap: 8px;
      padding: 10px 20px;
      border-radius: 999px;
      border: 1px solid rgba(255,255,255,.12);
      background: rgba(255,255,255,.06);
      backdrop-filter: blur(10px);
      color: rgba(255,255,255,.9);
      font-weight: 700;
      font-size: .9rem;
      box-shadow: 0 4px 12px rgba(0,0,0,.1), inset 0 1px 0 rgba(255,255,255,.1);
      animation: fadeIn 0.8s ease 0.3s both;
      position: relative;
      overflow: hidden;
    }

    .pill::before {
      content: "";
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.1), transparent);
      animation: shine 3s infinite;
    }

    @keyframes shine {
      to { left: 100%; }
    }

    .title{
      margin-top: 24px;
      font-size: clamp(3rem, 8vw, 5.5rem);
      line-height: 1.05;
      font-weight: 900;
      letter-spacing: -2px;
      background: linear-gradient(135deg, #ffffff 0%, rgba(255,255,255,.7) 100%);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      text-shadow: none;
      animation: fadeIn 0.8s ease 0.4s both;
    }

    .sub{
      margin-top: 20px;
      color: var(--text-muted);
      font-size: 1.25rem;
      line-height: 1.7;
      max-width: 65ch;
      margin-inline: auto;
      font-weight: 500;
      animation: fadeIn 0.8s ease 0.5s both;
    }

    .actions{
      margin-top: 32px;
      display:flex;
      gap: 16px;
      flex-wrap:wrap;
      align-items:center;
      justify-content:center;
      animation: fadeIn 0.8s ease 0.6s both;
    }

    /* Stats */
    .stats{
      margin-top: 60px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 20px;
      animation: fadeIn 0.8s ease 0.7s both;
    }

    .stat{
      padding: 24px;
      border-radius: 20px;
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.08);
      backdrop-filter: blur(10px);
      transition: all 0.3s ease;
    }

    .stat:hover {
      transform: translateY(-4px);
      background: rgba(255,255,255,.06);
      border-color: rgba(255,255,255,.12);
      box-shadow: 0 12px 32px rgba(0,0,0,.2);
    }

    .stat strong{
      display:block;
      font-size: 2rem;
      font-weight: 900;
      letter-spacing:-.5px;
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .stat span{
      display:block;
      margin-top: 8px;
      color: var(--text-muted);
      font-weight: 600;
      font-size: .95rem;
    }

    /* Sections */
    section{ padding: 80px 0; }
    
    .head{
      text-align: center;
      max-width: 800px;
      margin: 0 auto 48px;
    }

    .head h3{
      font-size: clamp(2rem, 4vw, 3rem);
      font-weight: 900;
      letter-spacing:-1px;
      margin-bottom: 16px;
      background: linear-gradient(135deg, #ffffff 0%, rgba(255,255,255,.8) 100%);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .head p{
      color: var(--text-muted);
      font-size: 1.15rem;
      line-height: 1.7;
      font-weight: 500;
    }

    .grid3{ 
      display:grid; 
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
      gap: 24px; 
    }

    .grid2{ 
      display:grid; 
      grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); 
      gap: 24px; 
    }

    .card{
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.08);
      border-radius: 24px;
      padding: 32px;
      backdrop-filter: blur(10px);
      transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
      position: relative;
      overflow: hidden;
    }

    .card::before {
      content: "";
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 1px;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,.2), transparent);
      opacity: 0;
      transition: opacity 0.3s ease;
    }

    .card:hover{
      transform: translateY(-8px);
      background: rgba(255,255,255,.06);
      border-color: rgba(255,255,255,.15);
      box-shadow: 0 20px 40px rgba(0,0,0,.3), inset 0 1px 0 rgba(255,255,255,.1);
    }

    .card:hover::before {
      opacity: 1;
    }

    .card h4{
      font-size: 1.35rem;
      font-weight: 800;
      margin-bottom: 12px;
      color: white;
    }

    .card p{
      color: var(--text-muted);
      line-height: 1.7;
      font-weight: 500;
    }

    /* Carousel */
    .carousel{
      position: relative;
      border-radius: 24px;
      overflow: hidden;
      border: 1px solid rgba(255,255,255,.08);
      background: rgba(255,255,255,.04);
      backdrop-filter: blur(10px);
      box-shadow: 0 20px 40px rgba(0,0,0,.3);
    }

    .carousel-track{
      display: flex;
      transform: translateX(0%);
      transition: transform .6s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .carousel-slide{
      min-width: 100%;
      aspect-ratio: 16 / 9;
      position: relative;
    }

    .carousel-slide::after{
      content:"";
      position:absolute;
      inset:0;
      background: linear-gradient(to top, rgba(0,0,0,.5), rgba(0,0,0,0));
      pointer-events:none;
    }

    .carousel img{
      width: 100%;
      height: 100%;
      object-fit: cover;
      display:block;
    }

    .carousel-btn{
      position:absolute;
      top:50%;
      transform: translateY(-50%);
      width: 48px;
      height: 48px;
      border-radius: 12px;
      border: 1px solid rgba(255,255,255,.15);
      background: rgba(0,0,0,.4);
      backdrop-filter: blur(10px);
      color: rgba(255,255,255,.9);
      font-size: 24px;
      line-height: 0;
      cursor: pointer;
      display:flex;
      align-items:center;
      justify-content:center;
      transition: all 0.3s ease;
    }

    .carousel-btn:hover{ 
      background: rgba(0,0,0,.6);
      transform: translateY(-50%) scale(1.1);
    }
    
    .carousel-btn.prev{ left: 16px; }
    .carousel-btn.next{ right: 16px; }

    .carousel-dots{
      position:absolute;
      left: 0;
      right: 0;
      bottom: 20px;
      display:flex;
      gap: 10px;
      justify-content:center;
    }

    .dot{
      width: 8px;
      height: 8px;
      border-radius: 999px;
      border: 1px solid rgba(255,255,255,.3);
      background: rgba(255,255,255,.2);
      cursor:pointer;
      transition: all 0.3s ease;
    }

    .dot:hover {
      background: rgba(255,255,255,.4);
      transform: scale(1.2);
    }

    .dot.is-active{
      width: 24px;
      background: rgba(255,255,255,.9);
      border-color: rgba(255,255,255,.8);
    }

    .img2{
      display: grid;
      grid-template-columns: repeat(2, minmax(0, 1fr));
      gap: 24px;
    }

    .img-card{
      border-radius: 24px;
      overflow: hidden;
      border: 1px solid rgba(255,255,255,.08);
      background: rgba(255,255,255,.04);
      backdrop-filter: blur(10px);
      box-shadow: 0 20px 40px rgba(0,0,0,.3);
      transition: all 0.4s ease;
    }

    .img-card:hover {
      transform: translateY(-8px) scale(1.02);
      box-shadow: 0 24px 48px rgba(0,0,0,.4);
    }

    .img-card img{
      width: 100%;
      height: 280px;
      object-fit: cover;
      display: block;
      transition: transform 0.4s ease;
    }

    .img-card:hover img {
      transform: scale(1.05);
    }

    /* Footer */
    footer{
      padding: 48px 0;
      border-top: 1px solid rgba(255,255,255,.06);
      background: rgba(10,10,15,.5);
      backdrop-filter: blur(10px);
    }

    .footer-grid{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap: 32px;
      flex-wrap:wrap;
    }

    .small{
      color: var(--text-muted);
      line-height: 1.7;
      max-width: 70ch;
      font-weight: 500;
      font-size: .95rem;
    }

    footer a {
      transition: color 0.2s ease;
    }

    footer a:hover {
      color: var(--text);
    }

    /* Responsive */
    @media (max-width: 980px){
      .img2{ grid-template-columns: 1fr; }
      .img-card img{ height: 240px; }
      .grid2{ grid-template-columns: 1fr; }
      .grid3{ grid-template-columns: 1fr; }
      nav.menu{ display:none; }
      .hero { padding: 60px 0 40px; }
      .title { font-size: clamp(2.5rem, 10vw, 4rem); }
    }

    @media (max-width: 640px){
      .stats { grid-template-columns: 1fr; }
      .actions { flex-direction: column; width: 100%; }
      .btn { width: 100%; justify-content: center; }
    }
  </style>
</head>

<body>
  <header class="topbar">
    <div class="wrap topbar-inner">
      <div class="brand">
        <div class="logo" aria-hidden="true"></div>
        <span>EduSecure</span>
      </div>

      <nav class="menu" aria-label="Navegación principal">
        <a href="#que-es">Qué es</a>
        <a href="#como-funciona">Cómo funciona</a>
        <a href="#funciones">Funciones</a>
        <a href="#contacto">Contacto</a>
      </nav>

      @if (Route::has('login'))
        <nav class="cta" aria-label="Acceso">
          @auth
            <a class="btn btn-primary" href="{{ url('/dashboard') }}">Dashboard</a>
          @else
            <a class="btn btn-primary" href="{{ route('login') }}">Iniciar sesión</a>
            @if (Route::has('register'))
              <a class="btn btn-ghost" href="{{ route('register') }}">Registrarse</a>
            @endif
          @endauth
        </nav>
      @endif
    </div>
  </header>

  <main>
    <section class="hero" id="home">
      <div class="wrap">
        <div class="hero-content">
          <div class="pill">✨ Vigilancia con visión por computadora</div>
          <h1 class="title">EDUSECURE</h1>

          <p class="sub">
            Plataforma de exámenes y clases vigiladas en tiempo real con visión por computadora: 
            detección de rostro, pérdida de rostro y señales de atención.
          </p>

          <div class="actions">
            <a class="btn btn-primary" href="{{ Route::has('login') ? route('login') : '#contacto' }}">
              Empezar ahora →
            </a>
            <a class="btn btn-ghost" href="#como-funciona">Ver cómo funciona</a>
          </div>

          <div class="stats">
            <div class="stat">
              <strong>Tiempo real</strong>
              <span>Alertas al instante</span>
            </div>
            <div class="stat">
              <strong>Rostro</strong>
              <span>Detectado / perdido</span>
            </div>
            <div class="stat">
              <strong>Atención</strong>
              <span>Desvíos de mirada</span>
            </div>
            <div class="stat">
              <strong>Registro</strong>
              <span>Métricas por sesión</span>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section id="que-es">
      <div class="wrap">
        <div class="head">
          <h3>Evaluaciones confiables</h3>
          <p>EduSecure ayuda a reducir suplantación y distracciones con estados simples y monitoreo automatizado.</p>
        </div>

        <div class="carousel" data-carousel>
          <div class="carousel-track" data-track>
            <div class="carousel-slide">
              <img src="https://www.e-dea.co/hubfs/2021/Blog%202021/sistemas%20de%20monitoreo%20de%20red.jpeg" alt="Monitoreo en tiempo real">
            </div>
            <div class="carousel-slide">
              <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQb2PhF59g5dNXiy4eES2gJI96f4we9DI26_Q&s" alt="Detección de rostro y atención">
            </div>
            <div class="carousel-slide">
              <img src="https://reportei.com/wp-content/uploads/2023/08/016-4.png" alt="Panel de métricas por sesión">
            </div>
          </div>

          <button class="carousel-btn prev" type="button" aria-label="Anterior" data-prev>‹</button>
          <button class="carousel-btn next" type="button" aria-label="Siguiente" data-next>›</button>

          <div class="carousel-dots" aria-label="Seleccionar imagen">
            <button class="dot is-active" type="button" aria-label="Ir a imagen 1" data-dot="0"></button>
            <button class="dot" type="button" aria-label="Ir a imagen 2" data-dot="1"></button>
            <button class="dot" type="button" aria-label="Ir a imagen 3" data-dot="2"></button>
          </div>
        </div>

        <div class="grid3" style="margin-top:32px;">
          <div class="card">
            <h4>Para exámenes y clases</h4>
            <p>Úsalo en quizzes, parciales, prácticas y sesiones donde se requiere control de atención.</p>
          </div>
          <div class="card">
            <h4>Indicadores claros</h4>
            <p>Estados directos para evitar confusión: rostro OK, sin rostro y rostro perdido.</p>
          </div>
          <div class="card">
            <h4>Integrable</h4>
            <p>Conecta con tu backend (por ejemplo WebSocket/FastAPI) para métricas por evaluación.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="como-funciona">
      <div class="wrap">
        <div class="head">
          <h3>Cómo funciona</h3>
          <p>Cámara → análisis → eventos → métricas.</p>
        </div>

        <div class="img2" style="margin-bottom:32px;">
          <div class="img-card">
            <img src="https://images.unsplash.com/photo-1555949963-ff9fe0c870eb?auto=format&fit=crop&w=1600&q=80" alt="Captura y procesamiento de video">
          </div>
          <div class="img-card">
            <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1600&q=80" alt="Monitoreo y métricas">
          </div>
        </div>

        <div class="grid2">
          <div class="card">
            <h4>Flujo</h4>
            <p>1) Captura de cámara. 2) Visión por computadora (rostro/atención). 3) Alertas y conteos en el panel.</p>
          </div>
          <div class="card">
            <h4>Acceso seguro</h4>
            <p>El acceso al monitoreo puede quedar detrás de login para que solo entren docentes/supervisores.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="funciones">
      <div class="wrap">
        <div class="head">
          <h3>Funciones principales</h3>
          <p>Diseñado para que el docente vea lo importante sin métricas confusas.</p>
        </div>

        <div class="img2" style="margin-bottom:32px;">
          <div class="img-card">
            <img src="https://images.unsplash.com/photo-1526379095098-d400fd0bf935?auto=format&fit=crop&w=1600&q=80" alt="Detección de rostro y monitoreo">
          </div>
          <div class="img-card">
            <img src="https://images.unsplash.com/photo-1555949963-ff9fe0c870eb?auto=format&fit=crop&w=1600&q=80" alt="Análisis de video y eventos">
          </div>
        </div>

        <div class="grid3">
          <div class="card">
            <h4>Detección de rostro</h4>
            <p>Confirma presencia del estudiante durante la sesión.</p>
          </div>
          <div class="card">
            <h4>Pérdida / ausencia</h4>
            <p>Detecta cuando el rostro no está o se pierde el seguimiento.</p>
          </div>
          <div class="card">
            <h4>Desvíos de mirada</h4>
            <p>Señales de atención para identificar distracciones o conductas sospechosas.</p>
          </div>
        </div>
      </div>
    </section>

    <section id="contacto">
      <div class="wrap">
        <div class="head">
          <h3>Contacto</h3>
          <p>Para pilotos y despliegues institucionales.</p>
        </div>

        <div class="grid2">
          <div class="card">
            <h4>Soporte</h4>
            <p class="small">
              Correo: contacto@edusecure.local<br>
              País: Ecuador
            </p>
          </div>

          <div class="card">
            <h4>Formulario (demo)</h4>
            <form onsubmit="event.preventDefault(); alert('Enviado (demo)');">
              <div style="display:grid;gap:16px;">
                <input required placeholder="Nombre" style="padding:14px;border-radius:12px;border:1px solid rgba(255,255,255,.12);background:rgba(0,0,0,.3);color:rgba(255,255,255,.9);outline:none;font-family:inherit;transition:all 0.3s ease;" onfocus="this.style.borderColor='rgba(99,102,241,.5)'" onblur="this.style.borderColor='rgba(255,255,255,.12)'">
                <input required type="email" placeholder="Correo" style="padding:14px;border-radius:12px;border:1px solid rgba(255,255,255,.12);background:rgba(0,0,0,.3);color:rgba(255,255,255,.9);outline:none;font-family:inherit;transition:all 0.3s ease;" onfocus="this.style.borderColor='rgba(99,102,241,.5)'" onblur="this.style.borderColor='rgba(255,255,255,.12)'">
                <button class="btn btn-primary" type="submit" style="justify-content:center;width:100%;">
                  Enviar →
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer>
    <div class="wrap footer-grid">
      <div>
        <div class="brand">
          <div class="logo" aria-hidden="true"></div>
          <span>EduSecure</span>
        </div>
        <p class="small" style="margin-top:16px;">
          EduSecure: exámenes y clases vigiladas con visión por computadora, con métricas claras y supervisión en tiempo real.
        </p>
      </div>

      <div class="small">
        <div style="font-weight:800;color:rgba(255,255,255,.95);margin-bottom:12px;font-size:1rem;">Enlaces</div>
        <a href="#home" style="color:var(--text-muted);text-decoration:none;display:block;margin-bottom:8px;">Inicio</a>
        <a href="#como-funciona" style="color:var(--text-muted);text-decoration:none;display:block;margin-bottom:8px;">Cómo funciona</a>
        <a href="#contacto" style="color:var(--text-muted);text-decoration:none;display:block;">Contacto</a>
      </div>
    </div>
  </footer>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Carousel functionality
      document.querySelectorAll('[data-carousel]').forEach((carousel) => {
        const track = carousel.querySelector('[data-track]');
        const slides = Array.from(track.children);
        const prevBtn = carousel.querySelector('[data-prev]');
        const nextBtn = carousel.querySelector('[data-next]');
        const dots = Array.from(carousel.querySelectorAll('[data-dot]'));

        let index = 0;
        let timer = null;

        function render(){
          track.style.transform = `translateX(-${index * 100}%)`;
          dots.forEach((d) => d.classList.remove('is-active'));
          if (dots[index]) dots[index].classList.add('is-active');
        }

        function goTo(i){
          index = (i + slides.length) % slides.length;
          render();
        }

        function start(){
          stop();
          timer = setInterval(() => goTo(index + 1), 5000);
        }

        function stop(){
          if (timer) clearInterval(timer);
          timer = null;
        }

        prevBtn?.addEventListener('click', () => { goTo(index - 1); start(); });
        nextBtn?.addEventListener('click', () => { goTo(index + 1); start(); });

        dots.forEach((dot) => {
          dot.addEventListener('click', () => { 
            goTo(parseInt(dot.dataset.dot, 10)); 
            start();
          });
        });

        carousel.addEventListener('mouseenter', stop);
        carousel.addEventListener('mouseleave', start);

        render();
        start();
      });

      // Scroll animations
      const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      };

      const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
          }
        });
      }, observerOptions);

      // Observe cards and sections
      document.querySelectorAll('.card, .img-card, section').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(30px)';
        el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(el);
      });
    });
  </script>
</body>
</html>
