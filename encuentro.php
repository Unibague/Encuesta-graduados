<?php
// Página pública - sin autenticación requerida
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Encuentro de Graduados 2026 – Universidad de Ibagué</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --pink:   #e91e8c;
            --cyan:   #00d4e0;
            --yellow: #ffc107;
            --orange: #ff6d00;
            --purple: #7c3aed;
            --indigo: #4338ca;
            --navy:   #07111f;
            --navy2:  #0e1f38;
            --border: #e2e8f0;
            --text:   #1e293b;
            --muted:  #64748b;
        }

        html { scroll-behavior: smooth; }

        body {
            font-family: 'Inter', system-ui, sans-serif;
            background: var(--navy);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
            position: relative;
        }

        /* ══════════════════════════════════════
           FONDO ANIMADO — chispitas de color
        ══════════════════════════════════════ */
        .bg-canvas {
            position: fixed;
            inset: 0;
            pointer-events: none;
            z-index: 0;
            overflow: hidden;
        }

        /* Glows radiales de fondo */
        .bg-canvas::before {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse 55% 45% at 15% 20%,  rgba(233,30,140,.16)  0%, transparent 65%),
                radial-gradient(ellipse 45% 55% at 85% 15%,  rgba(0,212,224,.14)   0%, transparent 65%),
                radial-gradient(ellipse 50% 50% at 50% 60%,  rgba(124,58,237,.12)  0%, transparent 60%),
                radial-gradient(ellipse 40% 40% at 80% 80%,  rgba(255,193,7,.10)   0%, transparent 55%),
                radial-gradient(ellipse 60% 40% at 10% 80%,  rgba(255,109,0,.10)   0%, transparent 55%);
        }

        /* Chispitas — generadas con box-shadow */
        .bg-canvas::after {
            content: '';
            position: absolute;
            width: 3px; height: 3px;
            border-radius: 50%;
            background: rgba(255,255,255,.0);
            box-shadow:
                /* pink */
                8vw   12vh  0 1px rgba(233,30,140,.75),
                30vw  5vh   0 1px rgba(233,30,140,.55),
                55vw  18vh  0 2px rgba(233,30,140,.45),
                88vw  8vh   0 1px rgba(233,30,140,.65),
                70vw  45vh  0 1px rgba(233,30,140,.40),
                3vw   70vh  0 2px rgba(233,30,140,.50),
                92vw  72vh  0 1px rgba(233,30,140,.45),
                /* cyan */
                18vw  35vh  0 2px rgba(0,212,224,.70),
                45vw  10vh  0 1px rgba(0,212,224,.60),
                78vw  28vh  0 2px rgba(0,212,224,.55),
                62vw  65vh  0 1px rgba(0,212,224,.45),
                25vw  82vh  0 2px rgba(0,212,224,.50),
                95vw  50vh  0 1px rgba(0,212,224,.40),
                /* yellow */
                35vw  22vh  0 2px rgba(255,193,7,.70),
                65vw  12vh  0 1px rgba(255,193,7,.60),
                12vw  55vh  0 2px rgba(255,193,7,.50),
                82vw  58vh  0 1px rgba(255,193,7,.45),
                48vw  78vh  0 2px rgba(255,193,7,.40),
                /* orange */
                22vw  48vh  0 1px rgba(255,109,0,.55),
                58vw  38vh  0 2px rgba(255,109,0,.45),
                75vw  75vh  0 1px rgba(255,109,0,.40),
                6vw   90vh  0 2px rgba(255,109,0,.50),
                /* purple */
                40vw  55vh  0 2px rgba(124,58,237,.60),
                85vw  35vh  0 1px rgba(124,58,237,.50),
                15vw  75vh  0 2px rgba(124,58,237,.45),
                52vw  90vh  0 1px rgba(124,58,237,.40);
            animation: twinkle 6s ease-in-out infinite alternate;
        }

        @keyframes twinkle {
            0%   { opacity: .6; transform: scale(1); }
            50%  { opacity: 1;  transform: scale(1.3); }
            100% { opacity: .5; transform: scale(.9); }
        }

        /* ══════════════════════════════════════
           HERO
        ══════════════════════════════════════ */
        .hero {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 3.5rem 1.5rem 2rem;
            text-align: center;
        }

        /* Marco del logo */
        .logo-frame {
            display: inline-block;
            border-radius: 24px;
            overflow: hidden;
            box-shadow:
                0 0 0 1px rgba(255,255,255,.08),
                0 24px 64px rgba(0,0,0,.55),
                0 0 60px rgba(233,30,140,.22),
                0 0 100px rgba(124,58,237,.18);
            max-width: 340px;
            width: 82%;
            animation: floatLogo 4s ease-in-out infinite;
        }

        .logo-frame img {
            display: block;
            width: 100%;
            height: auto;
        }

        @keyframes floatLogo {
            0%, 100% { transform: translateY(0px)   rotate(0deg); }
            30%       { transform: translateY(-10px) rotate(.4deg); }
            70%       { transform: translateY(-6px)  rotate(-.3deg); }
        }

        /* Fallback sin imagen */
        .logo-fallback {
            display: none;
            padding: 1rem 0;
        }
        .logo-fallback h1 {
            color: #fff;
            font-size: clamp(2rem, 6vw, 3rem);
            font-weight: 900;
            line-height: 1.1;
        }
        .logo-fallback .g {
            background: linear-gradient(90deg, #f472b6, #818cf8, #22d3ee, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .logo-fallback p { color: rgba(255,255,255,.55); font-style: italic; margin-top:.4rem; }

        /* Badge fecha */
        .date-badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            margin-top: 1.6rem;
            padding: .5rem 1.35rem;
            border-radius: 50px;
            border: 1px solid rgba(255,255,255,.16);
            background: rgba(255,255,255,.06);
            backdrop-filter: blur(8px);
            color: rgba(255,255,255,.88);
            font-size: .875rem;
            font-weight: 600;
            letter-spacing: .01em;
        }

        .date-pip {
            width: 7px; height: 7px;
            border-radius: 50%;
            background: var(--cyan);
            box-shadow: 0 0 6px var(--cyan);
            animation: pipPulse 2s ease-in-out infinite;
        }

        @keyframes pipPulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .5; transform: scale(1.5); }
        }

        /* Conector hero → formulario */
        .hero-connector {
            margin-top: 2.2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .35rem;
            color: rgba(255,255,255,.3);
            font-size: .8rem;
            font-weight: 500;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        .connector-line {
            width: 1px;
            height: 36px;
            background: linear-gradient(to bottom, rgba(255,255,255,.25), transparent);
        }

        /* ══════════════════════════════════════
           SECCIÓN FORMULARIO
        ══════════════════════════════════════ */
        .form-section {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: center;
            padding: 0 1.25rem 4rem;
        }

        /* ══════════════════════════════════════
           CARD
        ══════════════════════════════════════ */
        .card {
            background: #fff;
            border-radius: 22px;
            box-shadow:
                0 0 0 1px rgba(255,255,255,.08),
                0 8px 16px rgba(0,0,0,.12),
                0 32px 80px rgba(0,0,0,.45);
            width: 100%;
            max-width: 580px;
            overflow: hidden;
        }

        .card-stripe {
            height: 4px;
            background: linear-gradient(90deg, #e91e8c, #7c3aed, #4338ca, #00d4e0, #ffc107);
            background-size: 200%;
            animation: stripeSlide 4s linear infinite;
        }

        @keyframes stripeSlide {
            0%   { background-position: 0% 0%; }
            100% { background-position: 200% 0%; }
        }

        .card-inner {
            padding: 2.25rem 2.25rem 2.5rem;
        }

        .card-head {
            text-align: center;
            padding-bottom: 1.5rem;
            margin-bottom: 1.75rem;
            border-bottom: 1px solid var(--border);
        }

        .card-head h2 {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--navy);
            letter-spacing: -.02em;
        }

        .card-head p {
            font-size: .83rem;
            color: var(--muted);
            margin-top: .3rem;
        }

        /* ── Sección interna ── */
        .fsection { margin-bottom: 1.75rem; }

        .fsection-label {
            font-size: .68rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #94a3b8;
            margin-bottom: .9rem;
            display: flex;
            align-items: center;
            gap: .6rem;
        }
        .fsection-label::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }

        /* ── Grid ── */
        .g2 { display: grid; grid-template-columns: 1fr 1fr; gap: .85rem; }
        .span2 { grid-column: 1 / -1; }

        /* ── Field ── */
        .field { display: flex; flex-direction: column; gap: .3rem; }

        .field > label {
            font-size: .77rem;
            font-weight: 600;
            color: #374151;
        }

        .field .req { color: #ef4444; }

        .field input {
            height: 42px;
            border: 1.5px solid #dde3ec;
            border-radius: 9px;
            padding: 0 .9rem;
            font-size: .9rem;
            font-family: inherit;
            color: var(--text);
            background: #fcfcfd;
            transition: border-color .15s, box-shadow .15s, background .15s;
            outline: none;
            width: 100%;
        }

        .field input::placeholder { color: #c0c9d8; }

        .field input:focus {
            border-color: var(--indigo);
            background: #fff;
            box-shadow: 0 0 0 3px rgba(67,56,202,.1);
        }

        .field input.err {
            border-color: #f87171;
            background: #fff8f8;
        }

        .ferror {
            font-size: .74rem;
            color: #ef4444;
            display: none;
        }

        /* ── Toggle radio ── */
        .radio-row {
            display: flex;
            gap: .6rem;
            flex-wrap: wrap;
        }

        .ropt { position: relative; }
        .ropt input { position: absolute; opacity: 0; pointer-events: none; }

        .ropt label {
            display: flex;
            align-items: center;
            gap: .55rem;
            padding: .65rem 1rem;
            border: 1.5px solid #dde3ec;
            border-radius: 9px;
            cursor: pointer;
            font-size: .855rem;
            font-weight: 500;
            color: #475569;
            white-space: nowrap;
            transition: border-color .15s, background .15s, color .15s;
            user-select: none;
        }

        .ropt label .dot {
            width: 15px; height: 15px;
            border: 2px solid #c8d0da;
            border-radius: 50%;
            flex-shrink: 0;
            position: relative;
            transition: border-color .15s, background .15s;
        }

        .ropt label .dot::after {
            content: '';
            position: absolute;
            inset: 3px;
            border-radius: 50%;
            background: #fff;
            transform: scale(0);
            transition: transform .15s;
        }

        /* Variante indigo */
        .ropt.indigo input:checked + label {
            border-color: var(--indigo);
            background: #eef2ff;
            color: var(--indigo);
            font-weight: 600;
        }
        .ropt.indigo input:checked + label .dot {
            background: var(--indigo);
            border-color: var(--indigo);
        }
        .ropt.indigo input:checked + label .dot::after { transform: scale(1); }

        /* Variante cyan */
        .ropt.teal input:checked + label {
            border-color: #0891b2;
            background: #ecfeff;
            color: #0e7490;
            font-weight: 600;
        }
        .ropt.teal input:checked + label .dot {
            background: #0891b2;
            border-color: #0891b2;
        }
        .ropt.teal input:checked + label .dot::after { transform: scale(1); }

        .field-hint {
            font-size: .75rem;
            color: #94a3b8;
            line-height: 1.5;
        }

        /* ── Divider ── */
        .hdivider {
            height: 1px;
            background: var(--border);
            margin: .25rem 0 1.5rem;
        }

        /* ── Alert ── */
        .alert-inline {
            padding: .75rem 1rem;
            border-radius: 8px;
            font-size: .83rem;
            margin-top: .75rem;
        }
        .alert-inline.danger { background:#fef2f2; border:1px solid #fecaca; color:#dc2626; }
        .alert-inline.ok     { background:#f0fdf4; border:1px solid #bbf7d0; color:#166534; }
        .alert-inline.err    { background:#fef2f2; border:1px solid #fecaca; color:#dc2626; }

        /* ── Botón submit ── */
        .btn-main {
            width: 100%;
            height: 48px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--indigo) 0%, var(--purple) 55%, var(--pink) 100%);
            color: #fff;
            font-family: inherit;
            font-size: .95rem;
            font-weight: 700;
            cursor: pointer;
            letter-spacing: .01em;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            margin-top: 1.5rem;
            transition: opacity .2s, transform .15s, box-shadow .2s;
            position: relative;
            overflow: hidden;
        }

        .btn-main::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(255,255,255,.08) 50%, transparent 100%);
            transform: translateX(-100%);
            transition: transform .5s;
        }

        .btn-main:hover:not(:disabled)::before { transform: translateX(100%); }

        .btn-main:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(67,56,202,.40);
        }

        .btn-main:disabled { opacity: .65; cursor: default; }

        /* Spinner */
        .spin {
            width: 16px; height: 16px;
            border: 2px solid rgba(255,255,255,.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: rot .65s linear infinite;
            display: none;
            flex-shrink: 0;
        }
        @keyframes rot { to { transform: rotate(360deg); } }

        /* ══════════════════════════════════════
           STEP 2
        ══════════════════════════════════════ */
        .step2-outer {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: center;
            padding: 2.5rem 1.25rem 4rem;
        }

        .step2-inner {
            width: 100%;
            max-width: 580px;
            display: flex;
            flex-direction: column;
            gap: 1.1rem;
        }

        /* Banner éxito */
        .success-banner {
            border-radius: 18px;
            background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4c1d95 100%);
            border: 1px solid rgba(99,102,241,.3);
            padding: 2rem;
            color: #fff;
            box-shadow: 0 12px 36px rgba(67,56,202,.35), 0 0 0 1px rgba(255,255,255,.05);
        }

        .check-wrap {
            width: 48px; height: 48px;
            border-radius: 50%;
            background: rgba(255,255,255,.12);
            border: 1.5px solid rgba(255,255,255,.25);
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 1rem;
        }

        .check-wrap svg {
            width: 24px; height: 24px;
            stroke: #a5b4fc;
            stroke-width: 2.5;
            fill: none;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .success-banner h2 { font-size: 1.3rem; font-weight: 800; margin-bottom: .3rem; }
        .success-banner p  { font-size: .85rem; opacity: .8; line-height: 1.6; }

        /* Update card */
        .update-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 16px rgba(0,0,0,.1), 0 32px 64px rgba(0,0,0,.4);
        }

        .update-head {
            padding: 1.4rem 2rem 1.25rem;
            border-bottom: 1px solid var(--border);
        }

        .update-head h3 { font-size: 1rem; font-weight: 800; color: var(--navy); }
        .update-head p  { font-size: .8rem; color: var(--muted); margin-top: .3rem; line-height: 1.5; }

        .update-body { padding: 1.5rem 2rem 2rem; }

        .field input[readonly] {
            background: #f8fafc;
            color: #94a3b8;
            cursor: default;
            border-color: #edf2f7;
        }

        .btn-update {
            width: 100%;
            height: 46px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(135deg, #0369a1, #0891b2);
            color: #fff;
            font-family: inherit;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;
            margin-top: 1.5rem;
            transition: opacity .2s, transform .15s, box-shadow .2s;
        }

        .btn-update:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(8,145,178,.35);
        }

        .btn-update:disabled { opacity: .65; cursor: default; }

        .btn-skip {
            width: 100%;
            height: 40px;
            border: 1.5px solid #e2e8f0;
            border-radius: 9px;
            background: transparent;
            color: #94a3b8;
            font-family: inherit;
            font-size: .83rem;
            font-weight: 500;
            cursor: pointer;
            margin-top: .6rem;
            transition: all .15s;
        }

        .btn-skip:hover { border-color: #94a3b8; color: #64748b; }

        /* ══════════════════════════════════════
           STEP DONE
        ══════════════════════════════════════ */
        .done-outer {
            position: relative;
            z-index: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 70vh;
            padding: 2rem 1.5rem;
        }

        .done-inner {
            text-align: center;
            max-width: 420px;
        }

        .done-ring {
            width: 72px; height: 72px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--indigo), var(--purple));
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 8px 24px rgba(67,56,202,.4), 0 0 40px rgba(124,58,237,.25);
            animation: doneGlow 3s ease-in-out infinite alternate;
        }

        @keyframes doneGlow {
            from { box-shadow: 0 8px 24px rgba(67,56,202,.4), 0 0 30px rgba(124,58,237,.2); }
            to   { box-shadow: 0 8px 36px rgba(67,56,202,.6), 0 0 60px rgba(124,58,237,.4); }
        }

        .done-ring svg {
            width: 34px; height: 34px;
            stroke: #fff; stroke-width: 2.5;
            fill: none; stroke-linecap: round; stroke-linejoin: round;
        }

        .done-inner h2 {
            color: #fff;
            font-size: 2rem;
            font-weight: 900;
            margin-bottom: .75rem;
            letter-spacing: -.02em;
        }

        .done-inner p {
            color: rgba(255,255,255,.55);
            font-size: .95rem;
            line-height: 1.75;
        }

        .done-inner strong { color: rgba(255,255,255,.85); }

        /* ── Footer ── */
        footer {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 1.5rem;
            color: rgba(255,255,255,.2);
            font-size: .77rem;
            border-top: 1px solid rgba(255,255,255,.05);
        }

        /* ── Responsive móvil ── */
        @media (max-width: 640px) {
            .hero { padding: 2.5rem 1rem 1.25rem; }
            .logo-frame { max-width: 260px; width: 75%; }
            .date-badge { font-size: .82rem; padding: .45rem 1.1rem; }
            .hero-connector { margin-top: 1.4rem; }
            .connector-line { height: 28px; }

            .form-section { padding: 0 .75rem 3rem; }
            .card { border-radius: 16px; }
            .card-inner { padding: 1.6rem 1.25rem 2rem; }
            .card-head h2 { font-size: 1.1rem; }

            .g2 { grid-template-columns: 1fr; gap: .75rem; }
            .span2 { grid-column: 1; }

            .ropt label { padding: .6rem .85rem; font-size: .84rem; gap: .45rem; }
            .ropt label .dot { width: 14px; height: 14px; }

            /* Acompañantes: grid 3 col centrado */
            #seccionAcomp .radio-row {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
            }
            #seccionAcomp .ropt { flex: unset; }
            #seccionAcomp .ropt label {
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
                gap: .3rem;
                padding: .65rem .3rem;
                font-size: .78rem;
            }

            .step2-outer { padding: 1.5rem .75rem 3rem; }
            .success-banner { padding: 1.5rem 1.25rem; }
            .success-banner h2 { font-size: 1.15rem; }
            .update-head { padding: 1.25rem 1.25rem 1rem; }
            .update-body { padding: 1.25rem 1.25rem 1.75rem; }

            .done-outer { min-height: 65vh; padding: 2rem 1rem; }
            .done-inner h2 { font-size: 1.6rem; }
            .done-ring { width: 60px; height: 60px; }
            .done-ring svg { width: 28px; height: 28px; }
        }

        /* Pantallas muy pequeñas (≤ 380px) */
        @media (max-width: 380px) {
            .logo-frame { max-width: 210px; }
            .card-inner { padding: 1.4rem 1rem 1.75rem; }
            .card-head h2 { font-size: 1rem; }
            .update-head, .update-body { padding-left: 1rem; padding-right: 1rem; }
            .ropt label { padding: .55rem .65rem; font-size: .8rem; }
            .date-badge { font-size: .77rem; padding: .4rem .9rem; }
            .fsection-label { font-size: .64rem; }

            #seccionAcomp .ropt label { font-size: .73rem; padding: .6rem .2rem; }
        }
    </style>
</head>
<body>

<!-- Fondo con chispitas -->
<div class="bg-canvas" aria-hidden="true"></div>

<!-- ═══════════════════════════════
     STEP 1 — REGISTRO
═══════════════════════════════ -->
<div id="step-registro">

    <section class="hero">

        <!-- Logo animado -->
        <div class="logo-frame" id="logoFrame">
            <img src="/images/encuentro-2026.png"
                 alt="Encuentro de Graduados 2026"
                 onerror="document.getElementById('logoFrame').style.display='none';
                          document.getElementById('logoFallback').style.display='block'">
        </div>

        <div class="logo-fallback" id="logoFallback">
            <h1>Encuentro de<br><span class="g">GRADUADOS</span> 2026</h1>
            <p>Entre amigos y recuerdos</p>
        </div>

        <!-- Fecha -->
        <div class="date-badge">
            <span class="date-pip"></span>
            19 de septiembre de 2026
        </div>

        <!-- Conector visual hacia el formulario -->
        <div class="hero-connector">
            <span>Regístrate</span>
            <div class="connector-line"></div>
        </div>

    </section>

    <div class="form-section">
        <div class="card">
            <div class="card-stripe"></div>
            <div class="card-inner">

                <div class="card-head">
                    <h2>Formulario de Registro</h2>
                    <p>Completa los campos para confirmar tu asistencia al evento</p>
                </div>

                <form id="formEncuentro" novalidate>

                    <!-- DATOS PERSONALES -->
                    <div class="fsection">
                        <div class="fsection-label">Datos personales</div>
                        <div class="g2">

                            <div class="field">
                                <label for="nombres">Nombres <span class="req">*</span></label>
                                <input type="text" id="nombres" placeholder="María Camila"
                                       maxlength="100" autocomplete="given-name">
                                <span class="ferror" id="err-nombres">Campo requerido.</span>
                            </div>

                            <div class="field">
                                <label for="apellidos">Apellidos <span class="req">*</span></label>
                                <input type="text" id="apellidos" placeholder="García López"
                                       maxlength="100" autocomplete="family-name">
                                <span class="ferror" id="err-apellidos">Campo requerido.</span>
                            </div>

                            <div class="field">
                                <label for="identificacion">N.° de identificación <span class="req">*</span></label>
                                <input type="text" id="identificacion" placeholder="1090123456"
                                       maxlength="20" inputmode="numeric">
                                <span class="ferror" id="err-identificacion">Ingresa un número válido.</span>
                            </div>

                            <div class="field">
                                <label for="celular">Celular <span class="req">*</span></label>
                                <input type="tel" id="celular" placeholder="300 123 4567"
                                       maxlength="20" autocomplete="tel">
                                <span class="ferror" id="err-celular">Campo requerido.</span>
                            </div>

                            <div class="field span2">
                                <label for="correo">Correo electrónico <span class="req">*</span></label>
                                <input type="email" id="correo" placeholder="correo@ejemplo.com"
                                       maxlength="150" autocomplete="email">
                                <span class="ferror" id="err-correo">Ingresa un correo válido.</span>
                            </div>

                        </div>
                    </div>

                    <!-- ASISTENCIA -->
                    <div class="fsection">
                        <div class="fsection-label">Confirmación de asistencia</div>

                        <div class="field">
                            <label style="margin-bottom:.55rem;">
                                ¿Confirmas tu asistencia? <span class="req">*</span>
                            </label>
                            <div class="radio-row">
                                <div class="ropt indigo">
                                    <input type="radio" name="asistencia" id="asi-si" value="si">
                                    <label for="asi-si"><span class="dot"></span>Sí, asistiré</label>
                                </div>
                                <div class="ropt indigo">
                                    <input type="radio" name="asistencia" id="asi-no" value="no">
                                    <label for="asi-no"><span class="dot"></span>No podré asistir</label>
                                </div>
                            </div>
                            <span class="ferror" id="err-asistencia">Selecciona una opción.</span>
                        </div>
                    </div>

                    <!-- ACOMPAÑANTES -->
                    <div class="fsection" id="seccionAcomp">
                        <div class="fsection-label">Acompañante adulto</div>

                        <div class="field">
                            <label style="margin-bottom:.35rem;">
                                ¿Llevarás acompañante adulto? <span class="req">*</span>
                            </label>
                            <p class="field-hint" style="margin-bottom:.6rem;">
                                Máximo 2 adultos — evento el 19 de septiembre de 2026.
                            </p>
                            <div class="radio-row" style="flex-wrap:nowrap;">
                                <div class="ropt teal" style="flex:1;">
                                    <input type="radio" name="acompanantes" id="acomp-0" value="0">
                                    <label for="acomp-0" style="justify-content:center;"><span class="dot"></span>Sin acompañante</label>
                                </div>
                                <div class="ropt teal" style="flex:1;">
                                    <input type="radio" name="acompanantes" id="acomp-1" value="1">
                                    <label for="acomp-1" style="justify-content:center;"><span class="dot"></span>1 acompañante</label>
                                </div>
                                <div class="ropt teal" style="flex:1;">
                                    <input type="radio" name="acompanantes" id="acomp-2" value="2">
                                    <label for="acomp-2" style="justify-content:center;"><span class="dot"></span>2 acompañantes</label>
                                </div>
                            </div>
                            <span class="ferror" id="err-acompanantes">Selecciona una opción.</span>
                        </div>
                    </div>

                    <div class="hdivider"></div>

                    <div id="formAlert" style="display:none;" class="alert-inline danger"></div>

                    <button type="submit" class="btn-main" id="btnRegistrar">
                        <span id="btnRegistrarText">Confirmar registro</span>
                        <span class="spin" id="btnSpinner"></span>
                    </button>

                </form>
            </div>
        </div>
    </div>

</div><!-- /#step-registro -->


<!-- ═══════════════════════════════
     STEP 2 — ACTUALIZAR DATOS
═══════════════════════════════ -->
<div id="step-actualizar" style="display:none;">
    <div class="step2-outer">
        <div class="step2-inner">

            <div class="success-banner">
                <div class="check-wrap">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <h2>¡Registro confirmado!</h2>
                <p>
                    Tu inscripción al <strong>Encuentro de Graduados 2026</strong> fue guardada exitosamente.
                    Te esperamos el <strong>19 de septiembre de 2026</strong>.
                </p>
            </div>

            <div class="update-card">
                <div class="card-stripe"></div>
                <div class="update-head">
                    <h3>Actualiza tus datos de contacto</h3>
                    <p>
                        Mantén tu información actualizada para que la Universidad de Ibagué
                        pueda comunicarse contigo. Revisa y edita lo que necesites.
                    </p>
                </div>
                <div class="update-body">
                    <form id="formActualizar">
                        <input type="hidden" id="upd-cedula">
                        <div class="g2">

                            <div class="field">
                                <label>Nombres</label>
                                <input type="text" id="upd-nombres" readonly placeholder="—">
                            </div>

                            <div class="field">
                                <label>Apellidos</label>
                                <input type="text" id="upd-apellidos" readonly placeholder="—">
                            </div>

                            <div class="field span2">
                                <label>N.° de identificación</label>
                                <input type="text" id="upd-cedula-show" readonly placeholder="—">
                            </div>

                            <div class="field">
                                <label>Celular</label>
                                <input type="tel" id="upd-celular" placeholder="300 123 4567" maxlength="20">
                            </div>

                            <div class="field">
                                <label>Celular alterno <span style="color:#c0c9d8;font-weight:400;">(opcional)</span></label>
                                <input type="tel" id="upd-celular-alt" placeholder="300 765 4321" maxlength="20">
                            </div>

                            <div class="field span2">
                                <label>Correo electrónico</label>
                                <input type="email" id="upd-correo" placeholder="correo@ejemplo.com" maxlength="150">
                            </div>

                            <div class="field">
                                <label>Ciudad</label>
                                <input type="text" id="upd-ciudad" placeholder="Ibagué" maxlength="80">
                            </div>

                            <div class="field">
                                <label>Dirección</label>
                                <input type="text" id="upd-direccion" placeholder="Calle 123 # 45-67" maxlength="150">
                            </div>

                        </div>

                        <div id="sigaAlert" style="display:none;" class="alert-inline"></div>

                        <button type="submit" class="btn-update" id="btnActualizar">
                            <span id="btnActualizarText">Actualizar datos</span>
                            <span class="spin" id="btnActualizarSpinner"></span>
                        </button>

                        <button type="button" class="btn-skip" id="btnOmitir">
                            Omitir por ahora
                        </button>

                    </form>
                </div>
            </div>

        </div>
    </div>
</div>


<!-- ═══════════════════════════════
     STEP 3 — LISTO
═══════════════════════════════ -->
<div id="step-done" style="display:none;">
    <div class="done-outer">
        <div class="done-inner">
            <div class="done-ring">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
            </div>
            <h2>¡Todo listo!</h2>
            <p>
                Tu información fue guardada y actualizada.<br>
                Nos vemos en el <strong>Encuentro de Graduados 2026</strong>.
            </p>
        </div>
    </div>
</div>


<footer>Universidad de Ibagué &copy; 2026 &mdash; Encuentro de Graduados</footer>

<script>
/* ── utils ── */
const $ = id => document.getElementById(id);

function setErr(f, on) {
    const inp = $(f);
    const err = $('err-' + f);
    if (inp) inp.classList.toggle('err', on);
    if (err) err.style.display = on ? 'block' : 'none';
}

function clearAll() {
    ['nombres','apellidos','identificacion','celular','correo','asistencia','acompanantes']
        .forEach(f => setErr(f, false));
    const a = $('formAlert');
    a.style.display = 'none';
    a.textContent   = '';
}

/* ── STEP 1 ── */
$('formEncuentro').addEventListener('submit', async e => {
    e.preventDefault();
    clearAll();

    const nombres        = $('nombres').value.trim();
    const apellidos      = $('apellidos').value.trim();
    const identificacion = $('identificacion').value.trim();
    const celular        = $('celular').value.trim();
    const correo         = $('correo').value.trim();
    const asistencia     = document.querySelector('input[name="asistencia"]:checked');
    const acompanantes   = document.querySelector('input[name="acompanantes"]:checked');

    let ok = true;
    if (!nombres)                                            { setErr('nombres',        true); ok = false; }
    if (!apellidos)                                          { setErr('apellidos',      true); ok = false; }
    if (!/^\d+$/.test(identificacion))                      { setErr('identificacion', true); ok = false; }
    if (!celular)                                            { setErr('celular',        true); ok = false; }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo))        { setErr('correo',         true); ok = false; }
    if (!asistencia)                                         { setErr('asistencia',     true); ok = false; }
    if (!acompanantes)                                       { setErr('acompanantes',   true); ok = false; }
    if (!ok) return;

    const btn = $('btnRegistrar');
    $('btnRegistrarText').textContent = 'Enviando...';
    $('btnSpinner').style.display = 'block';
    btn.disabled = true;

    const payload = { nombres, apellidos, identificacion, celular, correo,
        asistencia: asistencia.value, acompanantes: acompanantes.value };

    try {
        const res  = await fetch('/api/save-encuentro.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        const json = await res.json();
        if (!res.ok || json.error) throw new Error(json.message || 'Error al registrar.');

        prefill(payload, json.datos_actuales || {});
        $('step-registro').style.display = 'none';
        $('step-actualizar').style.display = 'block';
        window.scrollTo({ top: 0, behavior: 'smooth' });

    } catch (err) {
        const a = $('formAlert');
        a.textContent   = err.message;
        a.style.display = 'block';
        $('btnRegistrarText').textContent = 'Confirmar registro';
        $('btnSpinner').style.display = 'none';
        btn.disabled = false;
    }
});

function prefill(s, e) {
    $('upd-cedula').value      = s.identificacion;
    $('upd-cedula-show').value = s.identificacion;
    $('upd-nombres').value     = e.name      || s.nombres;
    $('upd-apellidos').value   = e.last_name || s.apellidos;
    $('upd-celular').value     = e.mobile_phone              || s.celular;
    $('upd-celular-alt').value = e.alternative_mobile_phone || '';
    $('upd-correo').value      = e.email   || s.correo;
    $('upd-ciudad').value      = e.city    || '';
    $('upd-direccion').value   = e.address || '';
}

/* ── STEP 2 ── */
$('formActualizar').addEventListener('submit', async e => {
    e.preventDefault();
    const btn = $('btnActualizar');
    $('btnActualizarText').textContent = 'Actualizando...';
    $('btnActualizarSpinner').style.display = 'block';
    btn.disabled = true;

    const payload = {
        cedula:      $('upd-cedula').value,
        correo:      $('upd-correo').value.trim(),
        celular:     $('upd-celular').value.trim(),
        celular_alt: $('upd-celular-alt').value.trim(),
        ciudad:      $('upd-ciudad').value.trim(),
        direccion:   $('upd-direccion').value.trim(),
    };

    const al = $('sigaAlert');

    try {
        const res  = await fetch('/api/update-siga-encuentro.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        const json = await res.json();
        if (!res.ok || json.error) throw new Error(json.message || 'Error al actualizar.');

        al.textContent   = 'Datos actualizados correctamente en SIGA.';
        al.className     = 'alert-inline ok';
        al.style.display = 'block';

        setTimeout(() => {
            $('step-actualizar').style.display = 'none';
            $('step-done').style.display = 'block';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }, 1500);

    } catch (err) {
        al.textContent   = err.message;
        al.className     = 'alert-inline err';
        al.style.display = 'block';
        $('btnActualizarText').textContent = 'Guardar y actualizar en SIGA';
        $('btnActualizarSpinner').style.display = 'none';
        btn.disabled = false;
    }
});

$('btnOmitir').addEventListener('click', () => {
    $('step-actualizar').style.display = 'none';
    $('step-done').style.display = 'block';
    window.scrollTo({ top: 0, behavior: 'smooth' });
});

/* Bloquear acompañantes si elige No */
document.querySelectorAll('input[name="asistencia"]').forEach(r => {
    r.addEventListener('change', () => {
        const sec = $('seccionAcomp');
        if (r.value === 'no') {
            const n = document.getElementById('acomp-0');
            if (n) n.checked = true;
            sec.style.opacity = '.35';
            sec.style.pointerEvents = 'none';
        } else {
            sec.style.opacity = '1';
            sec.style.pointerEvents = '';
        }
    });
});
</script>

</body>
</html>
