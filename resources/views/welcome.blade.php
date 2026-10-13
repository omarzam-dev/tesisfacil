<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>TesisFácil — Corrige tu tesis en APA o ISO 690</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&family=Baloo+2:wght@600;700&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background: #fff; color: #1a1a1a; }
        h1, h2, .nav-brand, .plan-name { font-family: 'Baloo 2', sans-serif; }
        img { max-width: 100%; display: block; }

        /* NAVBAR */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.2rem 2rem;
            background: rgba(255,255,255,0.95);
            border-bottom: 1px solid #eee;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .nav-brand { font-size: 1.4rem; font-weight: 700; display: flex; align-items: center; gap: 0.6rem; color: #1a1a1a; }
        .nav-links { display: flex; gap: 1.5rem; align-items: center; }
        .nav-links a { color: #666; text-decoration: none; font-size: 0.9rem; transition: color 0.2s; }
        .nav-links a:hover { color: #12958A; }
        .btn-primary {
            background: #12958A;
            color: #fff;
            padding: 0.6rem 1.3rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            transition: opacity 0.2s;
        }
        .btn-primary:hover { opacity: 0.88; }

        /* HERO */
        .hero {
            display: grid;
            grid-template-columns: 1.1fr 1fr;
            gap: 3rem;
            align-items: center;
            max-width: 1150px;
            margin: 0 auto;
            padding: 4.5rem 2rem 5rem;
        }
        @media (max-width: 860px) {
            .hero { grid-template-columns: 1fr; padding-top: 3rem; text-align: center; }
            .hero-buttons { justify-content: center; }
        }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 0.4rem;
            background: #E6F5F3;
            color: #0d746b;
            padding: 0.35rem 1rem;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }
        .hero h1 { font-size: clamp(2.2rem, 4.5vw, 3.4rem); font-weight: 700; line-height: 1.18; margin-bottom: 1.3rem; color: #111; }
        .hero h1 span { color: #12958A; }
        .hero p { font-size: 1.05rem; color: #666; max-width: 480px; margin-bottom: 2rem; line-height: 1.7; }
        @media (max-width: 860px) { .hero p { margin-left: auto; margin-right: auto; } }
        .hero-buttons { display: flex; gap: 1rem; flex-wrap: wrap; }
        .btn-secondary {
            background: #fff;
            border: 1px solid #ddd;
            color: #444;
            padding: 0.75rem 1.8rem;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            transition: border-color 0.2s;
        }
        .btn-secondary:hover { border-color: #12958A; color: #12958A; }
        .btn-large { padding: 0.75rem 2rem; font-size: 1rem; border-radius: 8px; }

        .hero-image {
            position: relative;
            border-radius: 20px;
            overflow: hidden;
            background: linear-gradient(135deg, #E6F5F3, #FCEAEF);
            aspect-ratio: 4/5;
        }
        .hero-image img { width: 100%; height: 100%; object-fit: cover; }
        .hero-image-placeholder {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            flex-direction: column; gap: 0.5rem;
            color: #0d746b; font-size: 0.85rem; text-align: center; padding: 2rem;
        }

        /* DEMO */
        .demo-box {
            max-width: 620px;
            margin: 0 auto 5rem;
            background: #fafafa;
            border: 1px solid #eee;
            border-radius: 16px;
            padding: 2rem;
        }
        .demo-box > p { font-size: 0.85rem; color: #888; margin-bottom: 1rem; text-align: center; }
        .demo-check { display: flex; align-items: center; gap: 0.75rem; padding: 0.7rem 0; border-bottom: 1px solid #eee; font-size: 0.9rem; }
        .demo-check:last-child { border-bottom: none; }
        .demo-icon-ok { color: #2FAE64; background: #E3F5EA; width: 26px; height: 26px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .demo-icon-err { color: #E5426E; background: #FCE6EC; width: 26px; height: 26px; border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .demo-label { color: #333; }
        .demo-msg { color: #999; font-size: 0.8rem; margin-left: auto; text-align: right; }

        /* STATS */
        .stats {
            display: flex; justify-content: center; gap: 3rem; padding: 3rem 2rem;
            background: linear-gradient(90deg, #DDF2EF, #E3EFFB, #EDEAFB, #FCE6EC); border-top: 1px solid #CFDDDA; border-bottom: 1px solid #CFDDDA; flex-wrap: wrap;
        }
        .stat-item { text-align: center; }
        .stat-number { font-size: 2.2rem; font-weight: 900; font-family: 'Baloo 2', sans-serif; color: #12958A; }
        .stat-label { color: #888; font-size: 0.85rem; margin-top: 0.3rem; }

        /* FEATURES */
        .features { max-width: 1100px; margin: 5rem auto; padding: 0 2rem; }
        .section-title { text-align: center; font-size: 2rem; font-weight: 700; margin-bottom: 0.5rem; color: #111; }
        .section-sub { text-align: center; color: #888; margin-bottom: 3rem; font-size: 0.95rem; }
        .features-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem; }
        .feature-card { background: #fff; border: 1px solid #eee; border-radius: 16px; padding: 1.8rem; transition: border-color 0.2s, box-shadow 0.2s; }
        .feature-card:hover { border-color: #12958A55; box-shadow: 0 4px 20px rgba(0,0,0,0.04); }
        .feature-icon { width: 48px; height: 48px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-bottom: 1rem; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.06); }
        /* Colores pastel por tarjeta */
        .feature-card.c-celeste { background: #E3EFFB; border-color: #3B8ED822; } .c-celeste .feature-icon { color: #3B8ED8; }
        .feature-card.c-teal    { background: #DDF2EF; border-color: #12958A22; } .c-teal .feature-icon    { color: #12958A; }
        .feature-card.c-naranja { background: #FDEDE0; border-color: #E8792E22; } .c-naranja .feature-icon { color: #E8792E; }
        .feature-card.c-verde   { background: #E3F5EA; border-color: #2FAE6422; } .c-verde .feature-icon   { color: #2FAE64; }
        .feature-card.c-rosa    { background: #FCE6EC; border-color: #E5426E22; } .c-rosa .feature-icon    { color: #E5426E; }
        .feature-card.c-lavanda { background: #EDEAFB; border-color: #7A68D622; } .c-lavanda .feature-icon { color: #7A68D6; }
        .feature-ia-badge { display: inline-block; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; background: #fff; color: #12958A; padding: 0.15rem 0.5rem; border-radius: 999px; margin-left: 0.5rem; vertical-align: middle; }
        .feature-card h3 { font-size: 1rem; font-weight: 700; margin-bottom: 0.5rem; font-family: 'Inter', sans-serif; color: #111; }
        .feature-card p { color: #777; font-size: 0.875rem; line-height: 1.6; }
        .feature-card.soon { opacity: 0.6; }
        .feature-soon-badge {
            display: inline-block; font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;
            background: #f0f0f0; color: #999; padding: 0.15rem 0.5rem; border-radius: 999px; margin-left: 0.5rem;
        }

        /* IMAGEN SECUNDARIA */
        .photo-section {
            max-width: 1100px; margin: 5rem auto; padding: 0 2rem;
            display: grid; grid-template-columns: 1fr 1fr; gap: 3rem; align-items: center;
        }
        @media (max-width: 860px) { .photo-section { grid-template-columns: 1fr; text-align: center; } }
        .photo-section img { border-radius: 20px; aspect-ratio: 4/3; object-fit: cover; width: 100%; }
        .photo-placeholder {
            aspect-ratio: 4/3; border-radius: 20px; background: linear-gradient(135deg, #FCEAEF, #E6F5F3);
            display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 0.5rem;
            color: #0d746b; font-size: 0.85rem; text-align: center; padding: 2rem;
        }
        .photo-section h2 { font-size: 1.8rem; font-weight: 700; margin-bottom: 1rem; color: #111; }
        .photo-section p { color: #666; line-height: 1.7; }

        /* PLANES */
        .plans { max-width: 900px; margin: 5rem auto; padding: 0 2rem; }
        .plans-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 1.5rem; margin-top: 3rem; }
        .plan-card { background: #fff; border: 1px solid #eee; border-radius: 16px; padding: 2rem; }
        .plan-card.featured { border-color: #12958A; background: linear-gradient(135deg, #E6F5F3, #fff); }
        .plan-badge { display: inline-block; background: #12958A; color: #fff; font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 999px; margin-bottom: 1rem; }
        .plan-name { font-size: 1.2rem; font-weight: 700; margin-bottom: 0.5rem; color: #111; }
        .plan-price { font-size: 2.5rem; font-weight: 900; color: #12958A; margin-bottom: 0.3rem; font-family: 'Baloo 2', sans-serif; }
        .plan-price span { font-size: 0.9rem; color: #999; font-weight: 400; font-family: 'Inter', sans-serif; }
        .plan-trial { font-size: 0.8rem; color: #999; margin-bottom: 1.5rem; }
        .plan-ahorro { display: inline-block; background: #E8F7EE; color: #1F8A4C; font-size: 0.75rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 999px; margin-bottom: 0.4rem; }
        .plan-features { list-style: none; margin-bottom: 2rem; }
        .plan-features li { font-size: 0.875rem; color: #666; padding: 0.4rem 0; border-bottom: 1px solid #f0f0f0; }
        .plan-features li { display: flex; align-items: center; gap: 0.5rem; }
        .plan-features li::before { content: ''; width: 16px; height: 16px; flex-shrink: 0; background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%2312958A' stroke-width='3' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M5 13l4 4L19 7'/%3E%3C/svg%3E") no-repeat center / contain; }

        /* CTA */
        .cta { text-align: center; padding: 5rem 2rem; background: linear-gradient(135deg, #E6F5F3, #FCEAEF); }
        .cta h2 { font-size: 2.3rem; font-weight: 700; margin-bottom: 1rem; color: #111; }
        .cta p { color: #666; margin-bottom: 2rem; }

        /* FOOTER */
        footer { text-align: center; padding: 2rem; border-top: 1px solid #eee; color: #aaa; font-size: 0.8rem; }
        footer span { color: #12958A; font-weight: 600; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav>
    <div class="nav-brand">
        <svg width="26" height="26" viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
            <rect x="4" y="4" width="26" height="7" rx="2" fill="#2FAE64"/>
            <polygon points="30,11 30,18 12,29 4,29 4,22" fill="#12958A"/>
            <rect x="4" y="29" width="26" height="7" rx="2" fill="#E8792E"/>
            <circle cx="33" cy="8" r="3.5" fill="#E5426E"/>
        </svg>
        TesisFácil
    </div>
    <div class="nav-links">
        <a href="#features">Características</a>
        <a href="#plans">Planes</a>
        <a href="{{ route('login') }}">Iniciar sesión</a>
        <a href="{{ route('register') }}" class="btn-primary">Empezar gratis</a>
    </div>
</nav>

<!-- HERO -->
<section class="hero">
    <div>
        <div class="hero-badge"><svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.42a12.08 12.08 0 01.67 6.48A11.95 11.95 0 0012 20.06a11.95 11.95 0 00-6.83-2.99 12.08 12.08 0 01.66-6.48L12 14zm-4 6v-7.5l4-2.22"/></svg> Hecho para tesistas peruanos</div>
        <h1>Escribe tu tesis.<br><span>Sin errores de formato.</span></h1>
        <p>TesisFácil revisa automáticamente la estructura, las citas y las referencias de tu documento en APA o ISO 690, y organiza el avance de tu tesis por capítulos con control de versiones.</p>
        <div class="hero-buttons">
            <a href="{{ route('register') }}" class="btn-primary btn-large">Crear cuenta gratis</a>
            <a href="#plans" class="btn-secondary btn-large">Ver planes</a>
        </div>
    </div>

    <div class="hero-image">
        <img src="{{ asset('images/hero-estudiante.jpg') }}" alt="Estudiante universitaria trabajando en su tesis">
    </div>
</section>

<!-- DEMO -->
<div style="padding: 0 2rem;">
    <div class="demo-box">
        <p>Así se ve una revisión real</p>
        <div class="demo-check">
            <span class="demo-icon-ok"><svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
            <span class="demo-label">Márgenes</span>
            <span class="demo-msg">Correctos (2.54cm)</span>
        </div>
        <div class="demo-check">
            <span class="demo-icon-err"><svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg></span>
            <span class="demo-label">Fuente</span>
            <span class="demo-msg">Aptos Narrow no permitida</span>
        </div>
        <div class="demo-check">
            <span class="demo-icon-ok"><svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></span>
            <span class="demo-label">Citas sin referencia</span>
            <span class="demo-msg">Todas tienen su referencia</span>
        </div>
    </div>
</div>

<!-- STATS -->
<div class="stats">
    <div class="stat-item"><div class="stat-number">APA e ISO</div><div class="stat-label">APA 6, APA 7 e ISO 690</div></div>
    <div class="stat-item"><div class="stat-number">5</div><div class="stat-label">Capítulos estándar automáticos</div></div>
    <div class="stat-item"><div class="stat-number">∞</div><div class="stat-label">Versiones por capítulo</div></div>
    <div class="stat-item"><div class="stat-number">24h</div><div class="stat-label">Activación de cuenta</div></div>
</div>

<!-- FEATURES -->
<section class="features" id="features">
    <h2 class="section-title">Todo lo que necesitas para tu tesis</h2>
    <p class="section-sub">Una plataforma completa para tesistas e investigadores</p>
    <div class="features-grid">
        <div class="feature-card c-celeste">
            <div class="feature-icon"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v14a1 1 0 01-1 1H5a1 1 0 01-1-1V5zm4 0v14M4 9h16"/></svg></div>
            <h3>Estructura del documento</h3>
            <p>Detecta márgenes, interlineado y fuente incorrectos comparando tu .docx contra APA o el formato de tu universidad.</p>
        </div>
        <div class="feature-card c-teal">
            <div class="feature-icon"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg></div>
            <h3>Citas en el texto</h3>
            <p>Revisa el formato de tus citas parentéticas y narrativas, incluyendo la regla de "et al." según la edición.</p>
        </div>
        <div class="feature-card c-lavanda">
            <div class="feature-icon"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg></div>
            <h3>Cruce cita-referencia</h3>
            <p>Encuentra citas sin referencia y referencias que nunca citaste — algo que ningún corrector gratuito hace.</p>
        </div>
        <div class="feature-card c-verde">
            <div class="feature-icon"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg></div>
            <h3>Gestor de tesis</h3>
            <p>Organiza tu avance por los 5 capítulos estándar, con estado de cada uno (pendiente, en revisión, aprobado).</p>
        </div>
        <div class="feature-card c-celeste">
            <div class="feature-icon"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
            <h3>Control de versiones</h3>
            <p>Cada archivo que subes queda guardado como versión nueva, con historial completo y descarga de cualquiera.</p>
        </div>
        <div class="feature-card c-naranja">
            <div class="feature-icon"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>
            <h3>Revisor de matriz de consistencia <span class="feature-ia-badge">IA</span></h3>
            <p>La IA revisa que tus problemas, objetivos, hipótesis y variables calcen entre sí, y te dice qué corregir.</p>
        </div>
        <div class="feature-card c-rosa">
            <div class="feature-icon"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/></svg></div>
            <h3>Simulador de sustentación <span class="feature-ia-badge">IA</span></h3>
            <p>Practica con un jurado de IA: 5 preguntas sobre tu tesis, nota de 0 a 20 y consejos para cada respuesta.</p>
        </div>
        <div class="feature-card c-teal soon">
            <div class="feature-icon"><svg width="24" height="24" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg></div>
            <h3>Detector de IA <span class="feature-soon-badge">Próximo</span></h3>
            <p>Revisa si un texto fue generado por ChatGPT u otra IA. En camino.</p>
        </div>
    </div>
</section>

<!-- SEGUNDA IMAGEN -->
<section class="photo-section">
    <img src="{{ asset('images/estudiantes-grupo.jpg') }}" alt="Grupo de tesistas trabajando juntos">
    <div>
        <h2>Hecho por y para tesistas peruanos</h2>
        <p>Sabemos lo que es pelear con las normas APA o ISO a último minuto...</p>
    </div>
</section>

<!-- PLANES -->
<section class="plans" id="plans">
    <h2 class="section-title">Planes y precios</h2>
    <p class="section-sub">Empieza gratis, escala cuando lo necesites</p>
    <div class="plans-grid">
        <div class="plan-card">
            <div class="plan-name">Prueba gratuita</div>
            <div class="plan-price">S/ 0 <span>/14 días</span></div>
            <p class="plan-trial">Sin tarjeta de crédito</p>
            <ul class="plan-features">
                <li>Hasta 1 tesis</li>
                <li>10 revisiones APA / ISO al mes</li>
                <li>{{ config('ia.trial_usos', 3) }} usos de IA para probar</li>
                <li>Control de versiones</li>
            </ul>
            <a href="{{ route('register') }}" class="btn-primary" style="display:block;text-align:center;padding:0.75rem;">
                Empezar gratis
            </a>
        </div>
        @foreach ($plans as $plan)
            <div class="plan-card {{ $plan->destacado ? 'featured' : '' }}">
                @if ($plan->destacado)
                    <div class="plan-badge"><svg width="12" height="12" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" style="vertical-align:-1px"><path d="M9.05 2.93c.3-.92 1.6-.92 1.9 0l1.07 3.29a1 1 0 00.95.69h3.46c.97 0 1.37 1.24.59 1.81l-2.8 2.03a1 1 0 00-.36 1.12l1.07 3.29c.3.92-.76 1.69-1.54 1.12l-2.8-2.03a1 1 0 00-1.18 0l-2.8 2.03c-.78.57-1.84-.2-1.54-1.12l1.07-3.29a1 1 0 00-.36-1.12L2.98 8.72c-.78-.57-.38-1.81.59-1.81h3.46a1 1 0 00.95-.69l1.07-3.29z"/></svg> Recomendado</div>
                @endif
                <div class="plan-name">{{ $plan->name }}</div>
                <div class="plan-price">S/ {{ number_format($plan->price, 0) }} <span>{{ $plan->periodoTexto() }}</span></div>
                @if ($ahorro = $plan->ahorro($plans))
                    <div class="plan-ahorro">Ahorras S/ {{ number_format($ahorro, 0) }}</div>
                @endif
                <p class="plan-trial">{{ $plan->notaPago() }}</p>
                <ul class="plan-features">
                    <li>Hasta {{ $plan->max_tesis }} tesis</li>
                    <li>{{ $plan->max_revisiones }} revisiones APA / ISO al mes</li>
                    @if ($plan->max_ia > 0)
                        <li>{{ $plan->max_ia }} usos de IA / mes</li>
                    @endif
                    <li>Control de versiones</li>
                </ul>
                <a href="{{ route('register') }}" class="btn-primary" style="display:block;text-align:center;padding:0.75rem;">
                    Elegir plan
                </a>
            </div>
        @endforeach
    </div>
</section>

<!-- CTA FINAL -->
<section class="cta">
    <h2>¿Listo para escribir tu tesis sin errores?</h2>
    <p>Crea tu cuenta gratis y sube tu primer capítulo hoy mismo.</p>
    <a href="{{ route('register') }}" class="btn-primary btn-large">Crear cuenta gratis →</a>
</section>

<!-- FOOTER -->
<footer>
    <p>© {{ date('Y') }} <span>TesisFácil</span> — Un producto de <span>ZSystem</span> by Omar Zamudio</p>
</footer>

</body>
</html>
