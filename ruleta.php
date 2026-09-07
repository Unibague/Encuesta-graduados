<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ruleta – Encuentro de Graduados 2026</title>
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
}

body {
    font-family: 'Inter', system-ui, sans-serif;
    background: var(--navy);
    min-height: 100vh;
    color: #fff;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
}

/* ── Fondo oscuro con brillos neón (igual que el resto del sitio) ── */
.bg-canvas {
    position: fixed; inset: 0;
    pointer-events: none; z-index: 0; overflow: hidden;
}
.bg-canvas::before {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 55% 45% at 15% 20%,  rgba(233,30,140,.18)  0%, transparent 65%),
        radial-gradient(ellipse 45% 55% at 85% 15%,  rgba(0,212,224,.16)   0%, transparent 65%),
        radial-gradient(ellipse 50% 50% at 50% 60%,  rgba(124,58,237,.14)  0%, transparent 60%),
        radial-gradient(ellipse 40% 40% at 80% 80%,  rgba(255,109,0,.12)   0%, transparent 55%);
}
.bg-canvas::after {
    content: '';
    position: absolute;
    width: 3px; height: 3px; border-radius: 50%;
    background: transparent;
    box-shadow:
        8vw  12vh 0 1px rgba(233,30,140,.75),
        30vw  5vh 0 1px rgba(233,30,140,.55),
        55vw 18vh 0 2px rgba(233,30,140,.45),
        88vw  8vh 0 1px rgba(233,30,140,.65),
        18vw 35vh 0 2px rgba(0,212,224,.70),
        45vw 10vh 0 1px rgba(0,212,224,.60),
        78vw 28vh 0 2px rgba(0,212,224,.55),
        62vw 65vh 0 1px rgba(0,212,224,.45),
        35vw 22vh 0 2px rgba(124,58,237,.65),
        65vw 12vh 0 1px rgba(124,58,237,.55),
        12vw 55vh 0 2px rgba(124,58,237,.50),
        82vw 58vh 0 1px rgba(124,58,237,.45),
        22vw 48vh 0 1px rgba(255,109,0,.55),
        58vw 38vh 0 2px rgba(255,109,0,.45),
        75vw 75vh 0 1px rgba(255,109,0,.40),
        6vw  90vh 0 2px rgba(255,109,0,.50);
    animation: twinkle 6s ease-in-out infinite alternate;
}
@keyframes twinkle {
    0%   { opacity: .6; transform: scale(1); }
    50%  { opacity: 1;  transform: scale(1.3); }
    100% { opacity: .5; transform: scale(.9); }
}

/* ── Wrapper ── */
.page {
    position: relative; z-index: 2;
    max-width: 860px;
    margin: 0 auto;
    padding: 0 1.25rem 4rem;
}

/* ── Header bar ── */
.header-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1.75rem 0 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,.08);
    margin-bottom: 2.5rem;
    gap: 1rem;
}
.header-bar-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}
.header-logo {
    width: 56px; height: 56px;
    border-radius: 14px;
    object-fit: cover;
    flex-shrink: 0;
    box-shadow:
        0 0 0 1px rgba(255,255,255,.1),
        0 0 24px rgba(233,30,140,.35),
        0 8px 20px rgba(0,0,0,.4);
}
.header-bar h1 {
    font-size: clamp(1.3rem, 3.5vw, 2rem);
    font-weight: 900;
    color: #fff;
    letter-spacing: -.02em;
    line-height: 1.15;
}
.header-bar p {
    font-size: .78rem;
    color: rgba(255,255,255,.4);
    margin-top: .2rem;
}
.total-box {
    text-align: right;
    flex-shrink: 0;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.12);
    backdrop-filter: blur(8px);
    border-radius: 14px;
    padding: .7rem 1.2rem;
    min-width: 110px;
}
.total-box .lbl {
    font-size: .62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: rgba(255,255,255,.45);
}
.total-box .num {
    font-size: 2rem;
    font-weight: 900;
    line-height: 1;
    color: #fff;
}

/* ── Wheel ── */
.wheel-area {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 1.75rem;
}
.wheel-wrap {
    position: relative;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.pointer {
    position: absolute; top: -14px; left: 50%;
    transform: translateX(-50%); z-index: 10;
    width: 0; height: 0;
    border-left: 14px solid transparent;
    border-right: 14px solid transparent;
    border-top: 28px solid var(--cyan);
    filter: drop-shadow(0 2px 10px rgba(0,212,224,.85));
}
canvas#wheel {
    width: min(460px, 86vw);
    height: min(460px, 86vw);
    border-radius: 50%; display: block;
    box-shadow:
        0 0 0 4px rgba(255,255,255,.1),
        0 0 60px rgba(233,30,140,.35),
        0 0 100px rgba(0,212,224,.25),
        0 20px 60px rgba(0,0,0,.5);
}
.btn-spin {
    height: 52px; padding: 0 3.5rem;
    border: none; border-radius: 14px;
    background: linear-gradient(135deg, var(--indigo) 0%, var(--purple) 55%, var(--pink) 100%);
    color: #fff; font-family: inherit;
    font-size: 1rem; font-weight: 800;
    letter-spacing: .05em; cursor: pointer;
    transition: transform .15s, box-shadow .2s, opacity .2s;
    box-shadow: 0 8px 28px rgba(67,56,202,.4);
    position: relative; overflow: hidden;
}
.btn-spin::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(135deg, transparent, rgba(255,255,255,.1), transparent);
    transform: translateX(-100%); transition: transform .5s;
}
.btn-spin:hover:not(:disabled)::before { transform: translateX(100%); }
.btn-spin:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(67,56,202,.55); }
.btn-spin:disabled { opacity:.5; cursor:default; }

.btn-reload {
    background: transparent;
    border: 1px solid rgba(255,255,255,.15);
    color: rgba(255,255,255,.4);
    font-family: inherit; font-size: .75rem; font-weight: 500;
    padding: .35rem 1rem; border-radius: 8px;
    cursor: pointer; transition: all .15s;
    margin-top: -.5rem;
}
.btn-reload:hover { border-color: rgba(255,255,255,.4); color: rgba(255,255,255,.75); }

/* ── Ganador actual ── */
.current-winner {
    background: rgba(34,197,94,.1);
    border: 1px solid rgba(74,222,128,.35);
    backdrop-filter: blur(8px);
    border-radius: 16px;
    padding: 1.25rem 1.75rem;
    text-align: center;
    width: 100%;
    margin-bottom: 2rem;
    display: none;
    animation: fadeUp .4s ease;
}
@keyframes fadeUp { from{transform:translateY(12px);opacity:0} to{transform:translateY(0);opacity:1} }
.current-winner.visible { display: block; }
.current-winner .tag {
    font-size: .65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .1em;
    color: #4ade80; margin-bottom: .5rem;
}
.current-winner .cw-name {
    font-size: clamp(1.3rem, 4vw, 1.9rem);
    font-weight: 900; color: #fff; line-height: 1.2;
    text-shadow: 0 0 24px rgba(74,222,128,.5);
}

/* ── Historial ── */
.history-section { width: 100%; }

.history-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 1rem;
    flex-wrap: wrap;
    gap: .75rem;
}
.history-head-left { display: flex; align-items: center; gap: .75rem; }
.history-label {
    font-size: .68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: .1em;
    color: rgba(255,255,255,.4);
}
.winners-pill {
    background: rgba(251,191,36,.15);
    border: 1px solid rgba(251,191,36,.35);
    color: #fbbf24;
    font-size: .7rem; font-weight: 700;
    padding: .2rem .65rem; border-radius: 20px;
}
.history-actions { display: flex; gap: .6rem; }

.btn-pdf {
    height: 34px; padding: 0 1rem;
    border: none; border-radius: 8px;
    background: linear-gradient(135deg, #059669, #0891b2);
    color: #fff; font-family: inherit;
    font-size: .75rem; font-weight: 700;
    cursor: pointer; display: flex; align-items: center; gap: .4rem;
    transition: opacity .15s, transform .15s;
}
.btn-pdf:hover:not(:disabled) { transform: translateY(-1px); opacity: .9; }
.btn-pdf:disabled { opacity: .35; cursor: default; }

.btn-clear {
    height: 34px; padding: 0 .9rem;
    border: 1px solid rgba(255,255,255,.15);
    background: transparent; border-radius: 8px;
    color: rgba(255,255,255,.4); font-family: inherit;
    font-size: .75rem; font-weight: 500;
    cursor: pointer; transition: all .15s;
}
.btn-clear:hover:not(:disabled) { border-color: rgba(255,255,255,.4); color: rgba(255,255,255,.75); }
.btn-clear:disabled { opacity: .3; cursor: default; }

/* Lista de ganadores */
.winners-list { display: flex; flex-direction: column; gap: .5rem; }

.empty-state {
    text-align: center;
    padding: 2.5rem 1rem;
    color: rgba(255,255,255,.3);
    font-size: .82rem; line-height: 1.8;
    background: rgba(255,255,255,.03);
    border: 1px dashed rgba(255,255,255,.15);
    border-radius: 14px;
}
.empty-state .icon { display: none; }

.winner-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: .85rem 1.25rem;
    background: rgba(255,255,255,.05);
    border: 1px solid rgba(255,255,255,.1);
    backdrop-filter: blur(6px);
    border-radius: 12px;
    animation: slideIn .35s ease;
}
@keyframes slideIn { from{transform:translateX(-16px);opacity:0} to{transform:translateX(0);opacity:1} }
.winner-row:first-child {
    background: rgba(251,191,36,.12);
    border-color: rgba(251,191,36,.35);
}
.wr-num {
    width: 30px; height: 30px; border-radius: 50%;
    background: linear-gradient(135deg, var(--purple), var(--cyan));
    color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: .72rem; font-weight: 800; flex-shrink: 0;
}
.winner-row:first-child .wr-num {
    background: linear-gradient(135deg, #fde68a, #fbbf24);
    color: #78350f;
}
.wr-name { flex: 1; font-size: .9rem; font-weight: 700; color: #fff; }
.wr-time { font-size: .72rem; color: rgba(255,255,255,.35); flex-shrink: 0; }

/* ── Winner Modal ── */
.modal-overlay {
    position: fixed; inset: 0; z-index: 100;
    background: rgba(0,0,0,.75);
    backdrop-filter: blur(8px);
    display: flex; align-items: center; justify-content: center;
    padding: 1.5rem;
}
.winner-card {
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4c1d95 100%);
    border: 1px solid rgba(99,102,241,.4);
    border-radius: 24px;
    padding: 2.5rem 3rem;
    text-align: center;
    max-width: 420px; width: 100%;
    box-shadow: 0 24px 64px rgba(67,56,202,.5), 0 0 100px rgba(124,58,237,.3);
    animation: popIn .4s cubic-bezier(.34,1.56,.64,1);
}
@keyframes popIn { from{transform:scale(.6);opacity:0} to{transform:scale(1);opacity:1} }
.trophy { font-size: 3.5rem; display: block; margin-bottom: 1rem;
    animation: trophyBounce 1s ease-in-out infinite alternate; }
@keyframes trophyBounce { from{transform:translateY(0) rotate(-5deg)} to{transform:translateY(-8px) rotate(5deg)} }
.winner-label { font-size: .85rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .12em; color: var(--yellow); margin-bottom: .5rem; }
.winner-name { font-size: clamp(1.4rem, 5vw, 2rem); font-weight: 900; color: #fff;
    margin-bottom: 1.75rem; line-height: 1.2; word-break: break-word; }
.btn-close {
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
    color: #fff; font-family: inherit; font-size: .9rem; font-weight: 600;
    padding: .65rem 2rem; border-radius: 10px; cursor: pointer; transition: background .15s;
}
.btn-close:hover { background: rgba(255,255,255,.22); }

/* ── Confirm Modal ── */
.confirm-card {
    background: #fff;
    border-radius: 20px;
    padding: 2.25rem 2.25rem 1.75rem;
    text-align: center;
    max-width: 380px; width: 100%;
    box-shadow: 0 24px 64px rgba(15,23,42,.25);
    animation: popIn .3s cubic-bezier(.34,1.56,.64,1);
}
.confirm-icon {
    width: 48px; height: 48px;
    border-radius: 50%;
    background: #f1f5f9;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 1.1rem;
    font-size: 1.3rem;
}
.confirm-title {
    font-size: 1.1rem; font-weight: 800; color: #1e293b;
    margin-bottom: .5rem;
}
.confirm-text {
    font-size: .85rem; color: #94a3b8; line-height: 1.6;
    margin-bottom: 1.75rem;
}
.confirm-actions { display: flex; gap: .7rem; }
.confirm-actions button {
    flex: 1; height: 44px;
    border-radius: 10px;
    font-family: inherit; font-size: .85rem; font-weight: 700;
    cursor: pointer; transition: all .15s;
}
.btn-confirm-cancel {
    border: 1.5px solid #e2e8f0;
    background: #fff;
    color: #64748b;
}
.btn-confirm-cancel:hover { border-color: #cbd5e1; background: #f8fafc; }
.btn-confirm-ok {
    border: none;
    background: linear-gradient(135deg, #f43f5e, #e11d48);
    color: #fff;
}
.btn-confirm-ok:hover { transform: translateY(-1px); box-shadow: 0 8px 20px rgba(225,29,72,.3); }

/* ── Confetti ── */
.confetti-piece {
    position: fixed; top: -20px; opacity: 0;
    animation: confettiFall linear forwards; z-index: 99;
}
@keyframes confettiFall {
    0%   { transform:translateY(0) rotate(0deg);    opacity:1; }
    100% { transform:translateY(100vh) rotate(720deg); opacity:0; }
}

footer {
    position: relative; z-index: 1;
    text-align: center; padding: 1.5rem;
    color: rgba(255,255,255,.25); font-size: .75rem;
    border-top: 1px solid rgba(255,255,255,.08);
}

@media (max-width: 500px) {
    .header-bar { flex-direction: column; align-items: flex-start; }
    .total-box { align-self: flex-end; }
    .winner-card { padding: 2rem 1.25rem; }
    .history-head { flex-direction: column; align-items: flex-start; }
}
</style>
</head>
<body>

<div class="bg-canvas" aria-hidden="true"></div>

<div class="page">

    <!-- Header -->
    <div class="header-bar">
        <div class="header-bar-left">
            <img src="/images/encuentro-2026.png" alt="Encuentro de Graduados 2026" class="header-logo"
                 onerror="this.style.display='none'">
            <div>
                <h1>Ruleta de Participantes</h1>
                <p>Encuentro de Graduados 2026 &nbsp;•&nbsp; 19 de septiembre de 2026</p>
            </div>
        </div>
        <div class="total-box">
            <div class="lbl">Total de asistentes</div>
            <div class="num" id="totalNum">–</div>
        </div>
    </div>

    <!-- Ruleta centrada -->
    <div class="wheel-area">
        <div class="wheel-wrap">
            <div class="pointer"></div>
            <canvas id="wheel" width="500" height="500"></canvas>
        </div>
        <button class="btn-spin" id="btnSpin" disabled>Cargando...</button>
        <button class="btn-reload" onclick="loadParticipants()">↻ Actualizar participantes</button>
    </div>

    <!-- Ganador actual -->
    <div class="current-winner" id="currentWinner">
        <div class="tag">Ganador actual</div>
        <div class="cw-name" id="currentWinnerName"></div>
    </div>

    <!-- Historial abajo -->
    <div class="history-section">
        <div class="history-head">
            <div class="history-head-left">
                <span class="history-label">Historial</span>
                <span class="winners-pill" id="winnersCount">0</span>
                <span style="font-size:.72rem;color:rgba(255,255,255,.35);font-weight:500;">ganadores anteriores</span>
            </div>
            <div class="history-actions">
                <button class="btn-pdf" id="btnPdf" onclick="downloadPDF()" disabled>
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Descargar PDF
                </button>
                <button class="btn-clear" id="btnClear" onclick="clearHistory()" disabled>Limpiar</button>
            </div>
        </div>

        <div class="winners-list" id="winnersList">
            <div class="empty-state" id="emptyState">
                <span class="icon">🎯</span>
                Gira la ruleta para ver el historial de ganadores aquí
            </div>
        </div>
    </div>

</div><!-- /page -->

<footer>Universidad de Ibagué &copy; 2026 &mdash; Encuentro de Graduados</footer>

<!-- Modal -->
<div class="modal-overlay" id="modal" style="display:none;">
    <div class="winner-card">
        <span class="trophy">🏆</span>
        <div class="winner-label">¡Ganador!</div>
        <div class="winner-name" id="winnerName"></div>
        <button class="btn-close" onclick="closeModal()">Continuar</button>
    </div>
</div>

<!-- Confirm Modal -->
<div class="modal-overlay" id="confirmModal" style="display:none;">
    <div class="confirm-card">
        <div class="confirm-icon">🗑️</div>
        <div class="confirm-title">¿Limpiar el historial?</div>
        <p class="confirm-text">Se borrarán todos los ganadores registrados. Esta acción no se puede deshacer.</p>
        <div class="confirm-actions">
            <button class="btn-confirm-cancel" onclick="closeConfirmModal()">Cancelar</button>
            <button class="btn-confirm-ok" onclick="confirmClearHistory()">Sí, limpiar</button>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
/* Colores neón saturados de la paleta de marca, para la rueda sobre fondo oscuro. */
const COLORS = [
    '#e91e8c', '#00d4e0', '#7c3aed', '#ff6d00',
    '#4338ca', '#ff36a8', '#22f6ff', '#9d5cff',
    '#ff8f3d', '#6366f1', '#c026a3', '#0891b2',
    '#a78bfa', '#f97316', '#818cf8', '#d726ff',
];

let participants = [];
let winners      = [];
let currentAngle = 0;
let spinning     = false;

const canvas = document.getElementById('wheel');
const ctx    = canvas.getContext('2d');
const W = canvas.width, H = canvas.height;
const CX = W/2, CY = H/2, R = Math.min(CX,CY) - 8;

async function loadParticipants() {
    document.getElementById('totalNum').textContent = '…';
    try {
        const res  = await fetch('/api/participantes-encuentro.php');
        const data = await res.json();
        participants = data.participantes || [];
    } catch(e) { participants = []; }

    const n   = participants.length;
    const btn = document.getElementById('btnSpin');
    document.getElementById('totalNum').textContent = n;

    if (n >= 2) {
        btn.textContent = 'GIRAR RULETA';
        btn.disabled    = false;
    } else {
        btn.textContent = n === 1 ? '¡Solo 1 participante!' : 'Sin participantes';
        btn.disabled    = true;
    }
    drawWheel(currentAngle);
}

function drawWheel(angle) {
    ctx.clearRect(0, 0, W, H);
    const n = participants.length;

    if (n === 0) {
        ctx.beginPath(); ctx.arc(CX, CY, R, 0, 2*Math.PI);
        ctx.fillStyle = '#111a2e'; ctx.fill();
        ctx.fillStyle = 'rgba(255,255,255,.35)';
        ctx.font = 'bold 16px Inter, sans-serif';
        ctx.textAlign = 'center'; ctx.textBaseline = 'middle';
        ctx.fillText('Sin participantes', CX, CY);
        return;
    }

    const seg      = (2*Math.PI) / n;
    const fontSize = n > 24 ? 9 : n > 16 ? 11 : n > 10 ? 13 : 15;
    const maxLen   = n > 16 ? 14 : 20;

    for (let i = 0; i < n; i++) {
        const start = angle - Math.PI/2 + i*seg;
        const end   = start + seg;
        const mid   = start + seg/2;

        ctx.beginPath();
        ctx.moveTo(CX, CY);
        ctx.arc(CX, CY, R, start, end);
        ctx.closePath();
        ctx.fillStyle = COLORS[i % COLORS.length];
        ctx.fill();
        ctx.strokeStyle = 'rgba(255,255,255,.6)';
        ctx.lineWidth = 1.5;
        ctx.stroke();

        ctx.save();
        ctx.translate(CX, CY);
        ctx.rotate(mid);
        ctx.textAlign = 'right'; ctx.textBaseline = 'middle';
        ctx.fillStyle = '#fff';
        ctx.font = `bold ${fontSize}px Inter, sans-serif`;
        ctx.shadowColor = 'rgba(0,0,0,.6)'; ctx.shadowBlur = 4;
        let name = participants[i];
        if (name.length > maxLen) name = name.slice(0, maxLen-1) + '…';
        ctx.fillText(name, R - 14, 0);
        ctx.restore();
    }

    ctx.beginPath(); ctx.arc(CX, CY, R, 0, 2*Math.PI);
    ctx.strokeStyle = 'rgba(255,255,255,.25)'; ctx.lineWidth = 4; ctx.stroke();

    ctx.beginPath(); ctx.arc(CX, CY, 24, 0, 2*Math.PI); ctx.fillStyle = '#07111f'; ctx.fill();
    ctx.beginPath(); ctx.arc(CX, CY, 17, 0, 2*Math.PI); ctx.fillStyle = '#fff'; ctx.fill();
}

function spin() {
    if (spinning || participants.length < 2) return;
    spinning = true;

    const btn = document.getElementById('btnSpin');
    btn.disabled = true; btn.textContent = 'Girando...';

    const totalSpin = (7 + Math.random()*6) * 2*Math.PI + Math.random()*2*Math.PI;
    const startAng  = currentAngle;
    const duration  = 5000 + Math.random()*2000;
    const startTime = performance.now();

    function animate(now) {
        const t    = Math.min((now - startTime) / duration, 1);
        const ease = 1 - Math.pow(1-t, 4);
        currentAngle = startAng + totalSpin * ease;
        drawWheel(currentAngle);

        if (t < 1) {
            requestAnimationFrame(animate);
        } else {
            currentAngle = startAng + totalSpin;
            drawWheel(currentAngle);
            spinning = false;

            const n   = participants.length;
            const seg = 2*Math.PI / n;
            const a   = ((-currentAngle % (2*Math.PI)) + 2*Math.PI) % (2*Math.PI);
            const idx = Math.floor(a / seg) % n;

            setTimeout(() => {
                const winner = participants[idx];
                setCurrentWinner(winner);
                addToHistory(winner);
                showModal(winner);

                // Sacar al ganador de la rueda para que no pueda volver a salir
                participants.splice(idx, 1);
                document.getElementById('totalNum').textContent = participants.length;
                currentAngle = 0;
                drawWheel(currentAngle);

                if (participants.length >= 2) {
                    btn.disabled = false;
                    btn.textContent = 'GIRAR OTRA VEZ';
                } else {
                    btn.disabled = true;
                    btn.textContent = participants.length === 1 ? '¡Solo 1 participante!' : 'Sin participantes';
                }
            }, 500);
        }
    }
    requestAnimationFrame(animate);
}

function setCurrentWinner(name) {
    document.getElementById('currentWinnerName').textContent = name;
    document.getElementById('currentWinner').classList.add('visible');
}

function addToHistory(name) {
    const time = new Date().toLocaleTimeString('es-CO', {hour:'2-digit', minute:'2-digit'});
    winners.push({ name, time });

    const list  = document.getElementById('winnersList');
    const empty = document.getElementById('emptyState');
    if (empty) empty.remove();

    const row = document.createElement('div');
    row.className = 'winner-row';
    row.innerHTML = `
        <div class="wr-num">${winners.length}</div>
        <div class="wr-name">${name}</div>
        <div class="wr-time">${time}</div>
    `;
    list.prepend(row);

    document.getElementById('winnersCount').textContent = winners.length;
    document.getElementById('btnPdf').disabled   = false;
    document.getElementById('btnClear').disabled = false;
}

function clearHistory() {
    if (!winners.length) return;
    document.getElementById('confirmModal').style.display = 'flex';
}

function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
}

function confirmClearHistory() {
    winners = [];
    document.getElementById('winnersList').innerHTML =
        `<div class="empty-state" id="emptyState">
            <span class="icon">🎯</span>
            Gira la ruleta para ver el historial de ganadores aquí
        </div>`;
    document.getElementById('winnersCount').textContent = '0';
    document.getElementById('btnPdf').disabled   = true;
    document.getElementById('btnClear').disabled = true;
    document.getElementById('currentWinner').classList.remove('visible');
    closeConfirmModal();
}

function downloadPDF() {
    if (!winners.length) return;
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation:'portrait', unit:'mm', format:'a4' });
    const pageW = doc.internal.pageSize.getWidth();

    doc.setFillColor(26,58,107);
    doc.rect(0, 0, pageW, 38, 'F');
    doc.setTextColor(255,255,255);
    doc.setFontSize(18); doc.setFont('helvetica','bold');
    doc.text('Encuentro de Graduados 2026', pageW/2, 15, {align:'center'});
    doc.setFontSize(10); doc.setFont('helvetica','normal');
    doc.text('Universidad de Ibagué  •  19 de septiembre de 2026', pageW/2, 24, {align:'center'});
    doc.setFontSize(11); doc.setFont('helvetica','bold');
    doc.text('Historial de Ganadores – Ruleta de Participantes', pageW/2, 33, {align:'center'});

    doc.setTextColor(120,120,120); doc.setFontSize(8); doc.setFont('helvetica','normal');
    doc.text(`Generado el ${new Date().toLocaleString('es-CO')}`, pageW/2, 44, {align:'center'});

    let y = 54;
    winners.forEach((w, i) => {
        if (y > 270) { doc.addPage(); y = 20; }
        if (i % 2 === 0) { doc.setFillColor(240,244,255); doc.rect(14, y-5, pageW-28, 9, 'F'); }
        doc.setTextColor(26,58,107); doc.setFont('helvetica','bold'); doc.setFontSize(10);
        doc.text(`${i+1}.`, 18, y);
        doc.setTextColor(30,30,30); doc.setFont('helvetica','normal');
        doc.text(w.name, 28, y);
        doc.setTextColor(150,150,150); doc.setFontSize(8);
        doc.text(w.time, pageW-16, y, {align:'right'});
        y += 10;
    });

    const pages = doc.internal.getNumberOfPages();
    for (let p = 1; p <= pages; p++) {
        doc.setPage(p);
        doc.setTextColor(180,180,180); doc.setFontSize(7);
        doc.text(`Página ${p} de ${pages}  •  Universidad de Ibagué`, pageW/2, 290, {align:'center'});
    }

    doc.save('ganadores-encuentro-graduados-2026.pdf');
}

function showModal(name) {
    document.getElementById('winnerName').textContent = name;
    document.getElementById('modal').style.display   = 'flex';
    launchConfetti();
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
    document.querySelectorAll('.confetti-piece').forEach(el => el.remove());
}

function launchConfetti() {
    const colors = ['#e91e8c','#1A3A6B','#00d4e0','#7c3aed','#ff6d00','#fff'];
    for (let i = 0; i < 80; i++) {
        const el = document.createElement('div');
        el.className = 'confetti-piece';
        el.style.cssText = `
            left:${Math.random()*100}vw;
            background:${colors[Math.floor(Math.random()*colors.length)]};
            width:${6+Math.random()*8}px;
            height:${6+Math.random()*12}px;
            border-radius:${Math.random()>.5?'50%':'2px'};
            animation-duration:${2+Math.random()*3}s;
            animation-delay:${Math.random()*1.5}s;
        `;
        document.body.appendChild(el);
        el.addEventListener('animationend', () => el.remove());
    }
}

document.getElementById('btnSpin').addEventListener('click', spin);
loadParticipants();
</script>
</body>
</html>
