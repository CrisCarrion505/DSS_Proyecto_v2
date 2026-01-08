<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>EduSecure | Exámenes y clases vigiladas con visión por computadora</title>

  <style>
    :root{
      --brand:#4f46e5;
      --brand2:#7c3aed;
      --text: rgba(255,255,255,.92);
      --muted: rgba(255,255,255,.72);
      --shadow: 0 25px 60px rgba(0,0,0,.35);
    }

    *{ margin:0; padding:0; box-sizing:border-box; }
    html{ scroll-behavior:smooth; }

    body{
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
      color: var(--text);
      background: radial-gradient(1200px 600px at 70% 10%, rgba(255,255,255,.12), transparent 60%),
                  linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      overflow-x: hidden;
    }

    .wrap{ width:min(1200px, 92vw); margin-inline:auto; }

    /* Topbar */
    .topbar{
      position: sticky;
      top: 0;
      z-index: 50;
      backdrop-filter: blur(14px);
      background: rgba(10,15,30,.55);
      border-bottom: 1px solid rgba(255,255,255,.10);
    }
    .topbar-inner{
      display:flex;
      align-items:center;
      justify-content:space-between;
      padding: 14px 0;
      gap: 18px;
    }
    .brand{
      display:flex;
      align-items:center;
      gap:10px;
      font-weight: 900;
      letter-spacing:.4px;
    }
    .logo{
      width:34px;
      height:34px;
      border-radius: 10px;
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      box-shadow: 0 12px 30px rgba(79,70,229,.35);
      position:relative;
    }
    .logo::after{
      content:"";
      position:absolute;
      inset:9px;
      border-radius:7px;
      border:2px solid rgba(255,255,255,.65);
    }
    nav.menu{
      display:flex;
      gap: 18px;
      flex-wrap:wrap;
      align-items:center;
      justify-content:center;
    }
    nav.menu a{
      color: var(--muted);
      text-decoration:none;
      font-weight: 600;
      font-size: .95rem;
      padding: 8px 10px;
      border-radius: 999px;
      transition: .2s ease;
    }
    nav.menu a:hover{
      color: var(--text);
      background: rgba(255,255,255,.08);
    }

    .cta{
      display:flex;
      gap:10px;
      align-items:center;
      flex-wrap:wrap;
      justify-content:flex-end;
    }
    .btn{
      border:none;
      cursor:pointer;
      border-radius: 999px;
      padding: 12px 16px;
      font-weight: 800;
      letter-spacing:.3px;
      transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
      text-decoration:none;
      display:inline-flex;
      align-items:center;
      gap:10px;
      white-space:nowrap;
    }
    .btn-primary{
      color:white;
      background: linear-gradient(135deg, var(--brand), var(--brand2));
      box-shadow: 0 14px 35px rgba(124,58,237,.30);
    }
    .btn-primary:hover{ transform: translateY(-2px); }
    .btn-ghost{
      color: var(--text);
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.16);
    }
    .btn-ghost:hover{ transform: translateY(-2px); }

    /* Hero */
    .hero{
      position:relative;
      padding: 64px 0 38px;
      overflow:hidden;
      background:
        linear-gradient(105deg, rgba(11,16,32,.85), rgba(11,59,74,.35)),
        url("image.jpg");
      background-size: cover;
      background-position: center;
      border-bottom: 1px solid rgba(255,255,255,.10);
    }
    .hero-grid{
      display:grid;
      grid-template-columns: 1.15fr .85fr;
      gap: 26px;
      align-items:end;
      padding-bottom: 26px;
    }
    .pill{
      display:inline-flex;
      align-items:center;
      gap: 8px;
      padding: 8px 12px;
      border-radius: 999px;
      border: 1px solid rgba(255,255,255,.14);
      background: rgba(255,255,255,.07);
      color: rgba(255,255,255,.85);
      font-weight: 800;
      font-size: .9rem;
    }
    .title{
      margin-top: 10px;
      font-size: clamp(2.6rem, 7vw, 5.2rem);
      line-height: .92;
      font-weight: 950;
      letter-spacing: -1px;
      text-transform: uppercase;
      text-shadow: 0 30px 80px rgba(0,0,0,.45);
    }
    .sub{
      margin-top: 14px;
      color: rgba(255,255,255,.86);
      font-size: 1.1rem;
      line-height: 1.6;
      max-width: 60ch;
    }
    .actions{
      margin-top: 18px;
      display:flex;
      gap: 12px;
      flex-wrap:wrap;
      align-items:center;
    }
    .hero-card{
      background: rgba(0,0,0,.20);
      border: 1px solid rgba(255,255,255,.14);
      border-radius: 22px;
      padding: 18px;
      box-shadow: var(--shadow);
      backdrop-filter: blur(12px);
    }
    .kpis{
      display:grid;
      grid-template-columns: repeat(2, minmax(0,1fr));
      gap: 14px;
    }
    .kpi{
      padding: 16px;
      border-radius: 18px;
      background: rgba(255,255,255,.08);
      border: 1px solid rgba(255,255,255,.14);
    }
    .kpi strong{
      display:block;
      font-size: 1.8rem;
      font-weight: 950;
      letter-spacing:-.5px;
    }
    .kpi span{
      display:block;
      margin-top: 4px;
      color: rgba(255,255,255,.75);
      font-weight: 600;
      font-size: .95rem;
    }

    /* Sections */
    section{ padding: 56px 0; }
    .head{
      display:flex;
      align-items:flex-end;
      justify-content:space-between;
      gap: 18px;
      flex-wrap:wrap;
      margin-bottom: 18px;
    }
    .head h3{
      font-size: clamp(1.6rem, 2.5vw, 2.1rem);
      font-weight: 950;
      letter-spacing:-.4px;
    }
    .head p{
      color: rgba(255,255,255,.76);
      max-width: 70ch;
      line-height: 1.6;
    }

    .grid3{ display:grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 16px; }
    .grid2{ display:grid; grid-template-columns: 1fr 1fr; gap: 16px; align-items:stretch; }

    .card{
      background: rgba(10,15,30,.45);
      border: 1px solid rgba(255,255,255,.14);
      border-radius: 22px;
      padding: 18px;
      box-shadow: 0 18px 45px rgba(0,0,0,.25);
      backdrop-filter: blur(10px);
    }
    .card h4{
      font-size: 1.1rem;
      font-weight: 900;
      margin-bottom: 8px;
    }
    .card p{
      color: rgba(255,255,255,.76);
      line-height: 1.65;
      font-weight: 550;
    }

    /* Footer */
    footer{
      padding: 28px 0 44px;
      border-top: 1px solid rgba(255,255,255,.10);
      background: rgba(10,15,30,.35);
    }
    .footer-grid{
      display:flex;
      justify-content:space-between;
      align-items:flex-start;
      gap: 16px;
      flex-wrap:wrap;
    }
    .small{
      color: rgba(255,255,255,.70);
      line-height: 1.6;
      max-width: 70ch;
      font-weight: 550;
      font-size: .95rem;
    }
    .carousel{
        position: relative;
        border-radius: 22px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.14);
        background: rgba(10,15,30,.45);
        box-shadow: 0 18px 45px rgba(0,0,0,.25);
        }

        .carousel-track{
        display: flex;
        transform: translateX(0%);
        transition: transform .55s ease;
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
        background: linear-gradient(to top, rgba(0,0,0,.45), rgba(0,0,0,0));
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
        width: 42px;
        height: 42px;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,.18);
        background: rgba(0,0,0,.28);
        color: rgba(255,255,255,.9);
        font-size: 28px;
        line-height: 0;
        cursor: pointer;
        display:flex;
        align-items:center;
        justify-content:center;
        }

        .carousel-btn:hover{ background: rgba(0,0,0,.40); }
        .carousel-btn.prev{ left: 12px; }
        .carousel-btn.next{ right: 12px; }

        .carousel-dots{
        position:absolute;
        left: 0;
        right: 0;
        bottom: 12px;
        display:flex;
        gap: 8px;
        justify-content:center;
        }

        .dot{
        width: 10px;
        height: 10px;
        border-radius: 999px;
        border: 1px solid rgba(255,255,255,.25);
        background: rgba(255,255,255,.25);
        cursor:pointer;
        }

        .dot.is-active{
        background: rgba(255,255,255,.85);
        border-color: rgba(255,255,255,.75);
}

    .img2{
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .img-card{
        border-radius: 22px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.14);
        background: rgba(10,15,30,.45);
        box-shadow: 0 18px 45px rgba(0,0,0,.25);
    }

    .img-card img{
        width: 100%;
        height: 260px;
        object-fit: cover;
        display: block;
    }

    @media (max-width: 980px){
        .img2{ grid-template-columns: 1fr; }
        .img-card img{ height: 220px; }
    }

    @media (max-width: 980px){
      .hero-grid{ grid-template-columns: 1fr; align-items:start; }
      .grid3{ grid-template-columns: 1fr; }
      .grid2{ grid-template-columns: 1fr; }
      nav.menu{ display:none; }
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

      {{-- Mantiene la lógica de login/register/dashboard --}}
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
      <div class="wrap hero-grid">
        <div>
          <div class="pill">Pruebas y clases vigiladas con visión por computadora</div>
          <h1 class="title">EDUSECURE</h1>

          <p class="sub">
            Plataforma para realizar exámenes y clases vigiladas en tiempo real con visión por computadora:
            detección de rostro, pérdida de rostro y señales de atención.
          </p>

          <div class="actions">
            {{-- CTA: si quieres que siempre vaya a login, apunta a route('login') --}}
            <a class="btn btn-primary" href="{{ Route::has('login') ? route('login') : '#contacto' }}">Empezar</a>
            <a class="btn btn-ghost" href="#como-funciona">Ver cómo funciona</a>
          </div>
        </div>

        <aside class="hero-card" aria-label="Indicadores de valor">
          <div class="kpis">
            <div class="kpi"><strong>Tiempo real</strong><span>Alertas al instante</span></div>
            <div class="kpi"><strong>Rostro</strong><span>Detectado / perdido</span></div>
            <div class="kpi"><strong>Atención</strong><span>Desvíos de mirada</span></div>
            <div class="kpi"><strong>Registro</strong><span>Métricas por sesión</span></div>
          </div>
        </aside>
      </div>
    </section>
<section id="que-es">
  <div class="wrap">
    <div class="head">
      <h3>Evaluaciones confiables</h3>
      <p>EduSecure ayuda a reducir suplantación y distracciones con estados simples y monitoreo automatizado.</p>
    </div>

    {{-- Carrusel --}}
    <div class="carousel" data-carousel>
      <div class="carousel-track" data-track>
        <div class="carousel-slide">
           <img src="https://www.e-dea.co/hubfs/2021/Blog%202021/sistemas%20de%20monitoreo%20de%20red.jpeg" alt="Monitoreo en tiempo real">
        </div>
        <div class="carousel-slide">
           <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQb2PhF59g5dNXiy4eES2gJI96f4we9DI26_Q&s" alt="Detección de rostro y atención">        </div>
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

    <div class="grid3" style="margin-top:16px;">
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

            <!-- Imágenes arriba -->
            <div class="img2" style="margin-bottom:16px;">
            <div class="img-card">
                <img src="https://images.unsplash.com/photo-1555949963-ff9fe0c870eb?auto=format&fit=crop&w=1600&q=80"
                    alt="Captura y procesamiento de video">
            </div>
            <div class="img-card">
                <img src="https://images.unsplash.com/photo-1550751827-4bd374c3f58b?auto=format&fit=crop&w=1600&q=80"
                    alt="Monitoreo y métricas">
            </div>
            </div>

            <!-- Tus cards -->
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

        <!-- Imágenes arriba -->
        <div class="img2" style="margin-bottom:16px;">
        <div class="img-card">
            <img src="https://images.unsplash.com/photo-1526379095098-d400fd0bf935?auto=format&fit=crop&w=1600&q=80"
                alt="Detección de rostro y monitoreo">
        </div>
        <div class="img-card">
            <img src="https://images.unsplash.com/photo-1555949963-ff9fe0c870eb?auto=format&fit=crop&w=1600&q=80"
                alt="Análisis de video y eventos">
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
              <div style="display:grid;gap:10px;">
                <input required placeholder="Nombre"
                  style="padding:14px;border-radius:14px;border:1px solid rgba(255,255,255,.16);background:rgba(0,0,0,.18);color:rgba(255,255,255,.9);outline:none;">
                <input required type="email" placeholder="Correo"
                  style="padding:14px;border-radius:14px;border:1px solid rgba(255,255,255,.16);background:rgba(0,0,0,.18);color:rgba(255,255,255,.9);outline:none;">
                <button class="btn btn-primary" type="submit" style="justify-content:center;">
                  Enviar
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
        <p class="small" style="margin-top:10px;">
          EduSecure: exámenes y clases vigiladas con visión por computadora, con métricas claras y supervisión en tiempo real.
        </p>
      </div>

      <div class="small">
        <div style="font-weight:900;color:rgba(255,255,255,.9);margin-bottom:8px;">Enlaces</div>
        <a href="#home" style="color:rgba(255,255,255,.75);text-decoration:none;display:block;margin-bottom:6px;">Inicio</a>
        <a href="#como-funciona" style="color:rgba(255,255,255,.75);text-decoration:none;display:block;margin-bottom:6px;">Cómo funciona</a>
        <a href="#contacto" style="color:rgba(255,255,255,.75);text-decoration:none;display:block;">Contacto</a>
      </div>
    </div>
  </footer>
</body>

<script>
    document.addEventListener('DOMContentLoaded', () => {
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
        timer = setInterval(() => goTo(index + 1), 4500);
        }

        function stop(){
        if (timer) clearInterval(timer);
        timer = null;
        }

        prevBtn?.addEventListener('click', () => goTo(index - 1));
        nextBtn?.addEventListener('click', () => goTo(index + 1));

        dots.forEach((dot) => {
        dot.addEventListener('click', () => goTo(parseInt(dot.dataset.dot, 10)));
        });

        carousel.addEventListener('mouseenter', stop);
        carousel.addEventListener('mouseleave', start);

        render();
        start();
    });
    });
</script>

</html>
