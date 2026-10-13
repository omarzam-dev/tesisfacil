<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <title>TesisFácil — Corrige tu tesis en formato APA</title>
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
            display: inline-block;
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
        .demo-icon-ok { color: #2FAE64; font-weight: 700; }
        .demo-icon-err { color: #E5426E; font-weight: 700; }
        .demo-label { color: #333; }
        .demo-msg { color: #999; font-size: 0.8rem; margin-left: auto; text-align: right; }

        /* STATS */
        .stats {
            display: flex; justify-content: center; gap: 3rem; padding: 3rem 2rem;
            background: #fafafa; border-top: 1px solid #eee; border-bottom: 1px solid #eee; flex-wrap: wrap;
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
        .feature-icon { font-size: 2rem; margin-bottom: 1rem; }
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
        .plan-features li::before { content: '✓ '; color: #12958A; font-weight: 700; }

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
        <div class="hero-badge">🎓 Hecho para tesistas peruanos</div>
        <h1>Escribe tu tesis.<br><span>Sin errores de formato.</span></h1>
        <p>TesisFácil revisa automáticamente la estructura, las citas y las referencias de tu documento en formato APA, y organiza el avance de tu tesis por capítulos con control de versiones.</p>
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
            <span class="demo-icon-ok">✓</span>
            <span class="demo-label">Márgenes</span>
            <span class="demo-msg">Correctos (2.54cm)</span>
        </div>
        <div class="demo-check">
            <span class="demo-icon-err">✗</span>
            <span class="demo-label">Fuente</span>
            <span class="demo-msg">Aptos Narrow no permitida</span>
        </div>
        <div class="demo-check">
            <span class="demo-icon-ok">✓</span>
            <span class="demo-label">Citas sin referencia</span>
            <span class="demo-msg">Todas tienen su referencia</span>
        </div>
    </div>
</div>

<!-- STATS -->
<div class="stats">
    <div class="stat-item"><div class="stat-number">APA 6/7</div><div class="stat-label">Ambas ediciones soportadas</div></div>
    <div class="stat-item"><div class="stat-number">5</div><div class="stat-label">Capítulos estándar automáticos</div></div>
    <div class="stat-item"><div class="stat-number">∞</div><div class="stat-label">Versiones por capítulo</div></div>
    <div class="stat-item"><div class="stat-number">24h</div><div class="stat-label">Activación de cuenta</div></div>
</div>

<!-- FEATURES -->
<section class="features" id="features">
    <h2 class="section-title">Todo lo que necesitas para tu tesis</h2>
    <p class="section-sub">Una plataforma completa para tesistas e investigadores</p>
    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon">📐</div>
            <h3>Estructura del documento</h3>
            <p>Detecta márgenes, interlineado y fuente incorrectos comparando tu .docx contra las reglas oficiales APA.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">💬</div>
            <h3>Citas en el texto</h3>
            <p>Revisa el formato de tus citas parentéticas y narrativas, incluyendo la regla de "et al." según la edición.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🔗</div>
            <h3>Cruce cita-referencia</h3>
            <p>Encuentra citas sin referencia y referencias que nunca citaste — algo que ningún corrector gratuito hace.</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">📚</div>
            <h3>Gestor de tesis</h3>
            <p>Organiza tu avance por los 5 capítulos estándar, con estado de cada uno (pendiente, en revisión, aprobado).</p>
        </div>
        <div class="feature-card">
            <div class="feature-icon">🕓</div>
            <h3>Control de versiones</h3>
            <p>Cada archivo que subes queda guardado como versión nueva, con historial completo y descarga de cualquiera.</p>
        </div>
        <div class="feature-card soon">
            <div class="feature-icon">🤖</div>
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
        <p>Sabemos lo que es pelear con las normas APA a último minuto...</p>
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
                <li>10 revisiones APA / mes</li>
                <li>Control de versiones</li>
            </ul>
            <a href="{{ route('register') }}" class="btn-primary" style="display:block;text-align:center;padding:0.75rem;">
                Empezar gratis
            </a>
        </div>
        @foreach ($plans as $plan)
            <div class="plan-card {{ $plan->destacado ? 'featured' : '' }}">
                @if ($plan->destacado)
                    <div class="plan-badge">⭐ Recomendado</div>
                @endif
                <div class="plan-name">{{ $plan->name }}</div>
                <div class="plan-price">S/ {{ number_format($plan->price, 0) }} <span>{{ $plan->periodoTexto() }}</span></div>
                @if ($ahorro = $plan->ahorro($plans))
                    <div class="plan-ahorro">Ahorras S/ {{ number_format($ahorro, 0) }}</div>
                @endif
                <p class="plan-trial">{{ $plan->notaPago() }}</p>
                <ul class="plan-features">
                    <li>Hasta {{ $plan->max_tesis }} tesis</li>
                    <li>{{ $plan->max_revisiones }} revisiones APA / mes</li>
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
