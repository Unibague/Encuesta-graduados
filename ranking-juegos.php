<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ranking – Encuentro de Graduados 2026</title>
<link rel="icon" type="image/svg+xml" href="/images/favicon.svg">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
    --pink:   #e91e8c;
    --cyan:   #00d4e0;
    --yellow: #3B82F6;
    --orange: #ff6d00;
    --purple: #7c3aed;
    --indigo: #4338ca;
    --navy:   #07111f;
    --gold:   #fbbf24;
    --silver: #cbd5e1;
    --bronze: #fb923c;
}

body {
    font-family: 'Inter', system-ui, sans-serif;
    background: var(--navy);
    min-height: 100vh;
    -webkit-font-smoothing: antialiased;
    overflow-x: hidden;
    position: relative;
}

/* ── Fondo animado ── */
.bg-canvas { position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }
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
.bg-canvas::after {
    content: '';
    position: absolute;
    width: 3px; height: 3px;
    border-radius: 50%;
    background: rgba(255,255,255,.0);
    box-shadow:
        8vw   12vh  0 1px rgba(233,30,140,.75),
        30vw  5vh   0 1px rgba(233,30,140,.55),
        55vw  18vh  0 2px rgba(233,30,140,.45),
        88vw  8vh   0 1px rgba(233,30,140,.65),
        18vw  35vh  0 2px rgba(0,212,224,.70),
        45vw  10vh  0 1px rgba(0,212,224,.60),
        78vw  28vh  0 2px rgba(0,212,224,.55),
        62vw  65vh  0 1px rgba(0,212,224,.45),
        35vw  22vh  0 2px rgba(255,193,7,.70),
        65vw  12vh  0 1px rgba(255,193,7,.60),
        12vw  55vh  0 2px rgba(255,193,7,.50),
        82vw  58vh  0 1px rgba(255,193,7,.45),
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

.page { position: relative; z-index: 1; max-width: 1000px; margin: 0 auto; padding: 1.25rem 1.5rem 3rem; }

/* ── Header ── */
.header { text-align: center; margin-bottom: 2rem; }
.header-logo {
    display: block;
    width: 300px; max-width: 78vw; height: auto;
    border-radius: 24px;
    object-fit: cover;
    margin: 0 auto 1.25rem;
    box-shadow:
        0 0 0 1px rgba(255,255,255,.1),
        0 0 44px rgba(233,30,140,.4),
        0 0 80px rgba(124,58,237,.25),
        0 14px 36px rgba(0,0,0,.45);
    animation: floatLogo 4s ease-in-out infinite;
}
@keyframes floatLogo {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    30%      { transform: translateY(-8px) rotate(.4deg); }
    70%      { transform: translateY(-4px) rotate(-.3deg); }
}
.header .eyebrow {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .4rem 1.1rem; border-radius: 50px;
    border: 1px solid rgba(255,255,255,.16);
    background: rgba(255,255,255,.06);
    color: rgba(255,255,255,.75);
    font-size: .78rem; font-weight: 700; letter-spacing: .04em;
    margin-bottom: 1rem;
}
.header h1 {
    color: #fff;
    font-size: clamp(1.8rem, 5vw, 3rem);
    font-weight: 900;
    letter-spacing: -.02em;
    line-height: 1.1;
}
.header h1 .g {
    background: linear-gradient(90deg, #f472b6, #818cf8, #22d3ee, #fbbf24);
    -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
}
.header p { color: rgba(255,255,255,.4); font-size: .9rem; margin-top: .6rem; }

/* ── Podio ── */
.podium { display: flex; align-items: flex-end; justify-content: center; gap: 1rem; margin-bottom: 3rem; }
.podium-spot {
    display: flex; flex-direction: column; align-items: center;
    width: 30%; max-width: 220px;
    animation: floatUp .5s ease backwards;
}
.podium-spot:nth-child(1) { animation-delay: .1s; }
.podium-spot:nth-child(2) { animation-delay: 0s; }
.podium-spot:nth-child(3) { animation-delay: .2s; }
@keyframes floatUp { from { opacity: 0; transform: translateY(24px); } to { opacity: 1; transform: translateY(0); } }

.medal { font-size: 2.4rem; margin-bottom: .5rem; animation: bounce 2.4s ease-in-out infinite; }
.podium-spot:nth-child(2) .medal { animation-delay: .3s; }
.podium-spot:nth-child(3) .medal { animation-delay: .6s; }
@keyframes bounce { 0%,100% { transform: translateY(0); } 50% { transform: translateY(-8px); } }

.podium-name {
    color: #fff; font-weight: 800; font-size: .95rem; text-align: center;
    margin-bottom: .3rem; line-height: 1.25; min-height: 2.4em;
    display: flex; align-items: center; justify-content: center;
}
.podium-total { font-weight: 900; font-size: 1.1rem; margin-bottom: .75rem; }

.podium-bar {
    width: 100%; border-radius: 14px 14px 0 0;
    display: flex; align-items: flex-start; justify-content: center; padding-top: .6rem;
    font-size: 1.6rem; font-weight: 900; color: rgba(0,0,0,.35);
}
.podium-spot.first  .podium-bar  { height: 150px; background: linear-gradient(180deg, var(--gold), #f59e0b); box-shadow: 0 0 50px rgba(251,191,36,.4); }
.podium-spot.second .podium-bar  { height: 108px; background: linear-gradient(180deg, var(--silver), #94a3b8); box-shadow: 0 0 36px rgba(203,213,225,.25); }
.podium-spot.third  .podium-bar  { height: 84px;  background: linear-gradient(180deg, var(--bronze), #ea580c); box-shadow: 0 0 36px rgba(251,146,60,.3); }

.podium-spot.first  .podium-total { color: var(--gold); }
.podium-spot.second .podium-total { color: var(--silver); }
.podium-spot.third  .podium-total { color: var(--bronze); }

/* ── Lista 4-10 ── */
.rest-list { display: flex; flex-direction: column; gap: .65rem; }
.rest-row {
    display: flex; align-items: center; gap: 1rem;
    padding: .9rem 1.3rem; border-radius: 14px;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.08);
    backdrop-filter: blur(6px);
    animation: slideIn .4s ease backwards;
}
.rest-row:nth-child(1) { animation-delay: .05s; }
.rest-row:nth-child(2) { animation-delay: .1s; }
.rest-row:nth-child(3) { animation-delay: .15s; }
.rest-row:nth-child(4) { animation-delay: .2s; }
.rest-row:nth-child(5) { animation-delay: .25s; }
.rest-row:nth-child(6) { animation-delay: .3s; }
.rest-row:nth-child(7) { animation-delay: .35s; }
@keyframes slideIn { from { opacity: 0; transform: translateX(-16px); } to { opacity: 1; transform: translateX(0); } }

.rest-rank {
    width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
    background: rgba(255,255,255,.08);
    color: rgba(255,255,255,.7);
    display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .82rem;
}
.rest-name { flex: 1; color: #fff; font-weight: 700; font-size: .95rem; }
.rest-total { color: var(--cyan); font-weight: 800; font-size: .95rem; flex-shrink: 0; }

/* ── Estado vacío ── */
.empty-state {
    text-align: center; padding: 4rem 1.5rem;
    color: rgba(255,255,255,.35); font-size: .95rem; line-height: 1.8;
    border: 1px dashed rgba(255,255,255,.15); border-radius: 18px;
}

footer { position: relative; z-index: 1; text-align: center; padding: 1.5rem; color: rgba(255,255,255,.2); font-size: .77rem; }

@media (max-width: 640px) {
    .header-logo { width: 220px; }
    .podium { align-items: flex-end; gap: .6rem; }
    .podium-name { font-size: .78rem; min-height: 2.1em; }
    .podium-total { font-size: .95rem; }
    .medal { font-size: 1.8rem; }
    .podium-spot.first  .podium-bar { height: 110px; }
    .podium-spot.second .podium-bar { height: 82px; }
    .podium-spot.third  .podium-bar { height: 64px; }
    .rest-row { padding: .75rem 1rem; gap: .75rem; }
}
</style>
</head>
<body>

<div class="bg-canvas" aria-hidden="true"></div>

<div class="page">

    <div class="header">
        <img src="/images/encuentro-2026.png" alt="Encuentro de Graduados 2026" class="header-logo"
             onerror="this.style.display='none'">
        <div class="eyebrow">🏆 Encuentro de Graduados 2026</div>
        <h1>Ranking <span class="g">Final</span></h1>
        <p>Top 10 — suma de los 3 juegos</p>
    </div>

    <div id="rankingContent">
        <div class="empty-state">Cargando ranking…</div>
    </div>

</div>

<footer>Universidad de Ibagué &copy; 2026 &mdash; Encuentro de Graduados</footer>

<script>
const MEDALS = ['🥇', '🥈', '🥉'];

function render(ranking) {
    const container = document.getElementById('rankingContent');

    if (!ranking.length) {
        container.innerHTML = '<div class="empty-state">🎮<br>Aún no hay puntajes registrados.<br>¡Los juegos están por comenzar!</div>';
        return;
    }

    const top3 = ranking.slice(0, 3);
    const rest = ranking.slice(3, 10);

    // Orden visual del podio: 2do - 1ro - 3ro
    const order = [top3[1], top3[0], top3[2]];
    const classes = ['second', 'first', 'third'];

    let podiumHtml = '<div class="podium">';
    order.forEach((p, i) => {
        if (!p) { podiumHtml += `<div class="podium-spot ${classes[i]}"></div>`; return; }
        podiumHtml += `
            <div class="podium-spot ${classes[i]}">
                <div class="medal">${MEDALS[classes[i] === 'first' ? 0 : classes[i] === 'second' ? 1 : 2]}</div>
                <div class="podium-name">${p.nombre}</div>
                <div class="podium-total">${p.total} pts</div>
                <div class="podium-bar"></div>
            </div>`;
    });
    podiumHtml += '</div>';

    let restHtml = '';
    if (rest.length) {
        restHtml = '<div class="rest-list">' + rest.map((p, i) => `
            <div class="rest-row">
                <div class="rest-rank">${i + 4}</div>
                <div class="rest-name">${p.nombre}</div>
                <div class="rest-total">${p.total} pts</div>
            </div>
        `).join('') + '</div>';
    }

    container.innerHTML = podiumHtml + restHtml;
}

async function cargarRanking() {
    try {
        const res  = await fetch('/api/ranking-juegos.php');
        const data = await res.json();
        render(data.ranking || []);
    } catch (e) {
        // Mantiene el último ranking visible si falla una actualización puntual.
    }
}

cargarRanking();
setInterval(cargarRanking, 6000);
</script>
</body>
</html>
