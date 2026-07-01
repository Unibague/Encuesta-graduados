<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Ruleta – Encuentro de Graduados 2026</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
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
}

body {
    font-family: 'Inter', system-ui, sans-serif;
    background: var(--navy);
    min-height: 100vh;
    color: #fff;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
}

/* ── Fondo ── */
.bg-canvas {
    position: fixed; inset: 0;
    pointer-events: none; z-index: 0; overflow: hidden;
}
.bg-canvas::before {
    content: '';
    position: absolute; inset: 0;
    background:
        radial-gradient(ellipse 55% 45% at 15% 20%, rgba(233,30,140,.16) 0%, transparent 65%),
        radial-gradient(ellipse 45% 55% at 85% 15%, rgba(0,212,224,.14)  0%, transparent 65%),
        radial-gradient(ellipse 50% 50% at 50% 60%, rgba(124,58,237,.12) 0%, transparent 60%),
        radial-gradient(ellipse 40% 40% at 80% 80%, rgba(255,193,7,.10)  0%, transparent 55%),
        radial-gradient(ellipse 60% 40% at 10% 80%, rgba(255,109,0,.10)  0%, transparent 55%);
}
.bg-canvas::after {
    content: '';
    position: absolute;
    width: 3px; height: 3px; border-radius: 50%;
    background: transparent;
    box-shadow:
        8vw 12vh 0 1px rgba(233,30,140,.75), 30vw 5vh 0 1px rgba(233,30,140,.55),
        55vw 18vh 0 2px rgba(233,30,140,.45), 88vw 8vh 0 1px rgba(233,30,140,.65),
        18vw 35vh 0 2px rgba(0,212,224,.70),  45vw 10vh 0 1px rgba(0,212,224,.60),
        78vw 28vh 0 2px rgba(0,212,224,.55),  62vw 65vh 0 1px rgba(0,212,224,.45),
        35vw 22vh 0 2px rgba(255,193,7,.70),  65vw 12vh 0 1px rgba(255,193,7,.60),
        12vw 55vh 0 2px rgba(255,193,7,.50),  82vw 58vh 0 1px rgba(255,193,7,.45),
        40vw 55vh 0 2px rgba(124,58,237,.60), 85vw 35vh 0 1px rgba(124,58,237,.50);
    animation: twinkle 6s ease-in-out infinite alternate;
}
@keyframes twinkle {
    0%   { opacity:.6; transform:scale(1); }
    50%  { opacity:1;  transform:scale(1.3); }
    100% { opacity:.5; transform:scale(.9); }
}

/* ── Contador fijo arriba derecha ── */
.total-badge {
    position: fixed;
    top: 1.1rem; right: 1.25rem;
    z-index: 50;
    background: rgba(255,255,255,.08);
    border: 1px solid rgba(255,255,255,.18);
    backdrop-filter: blur(12px);
    border-radius: 14px;
    padding: .55rem 1.1rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: .1rem;
    min-width: 90px;
    box-shadow: 0 4px 20px rgba(0,0,0,.3);
}
.total-badge .num {
    font-size: 1.6rem;
    font-weight: 900;
    background: linear-gradient(90deg, var(--cyan), var(--purple));
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
}
.total-badge .lbl {
    font-size: .65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: rgba(255,255,255,.45);
}

/* ── Header ── */
.header {
    position: relative; z-index: 1;
    text-align: center;
    padding: 2.5rem 1rem 1.5rem;
}
.date-badge {
    display: inline-flex; align-items: center; gap: .5rem;
    padding: .4rem 1.2rem;
    border-radius: 50px;
    border: 1px solid rgba(255,255,255,.16);
    background: rgba(255,255,255,.06);
    backdrop-filter: blur(8px);
    color: rgba(255,255,255,.85);
    font-size: .8rem; font-weight: 600;
    margin-bottom: 1rem;
}
.date-pip {
    width: 7px; height: 7px; border-radius: 50%;
    background: var(--cyan);
    box-shadow: 0 0 6px var(--cyan);
    animation: pip 2s ease-in-out infinite;
}
@keyframes pip { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.5)} }
.header h1 {
    font-size: clamp(1.6rem, 4vw, 2.8rem);
    font-weight: 900;
    background: linear-gradient(90deg, #f472b6, #818cf8, #22d3ee, #fbbf24);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: .35rem;
    letter-spacing: -.02em;
}
.header p { color: rgba(255,255,255,.5); font-size: .9rem; }

/* ── Layout principal ── */
.main-layout {
    position: relative; z-index: 1;
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 2rem;
    max-width: 1100px;
    margin: 0 auto;
    padding: 0 1.5rem 3rem;
    align-items: start;
}

/* ── Wheel ── */
.wheel-area {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1.25rem;
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
    border-top: 28px solid var(--yellow);
    filter: drop-shadow(0 2px 6px rgba(255,193,7,.6));
}
canvas#wheel {
    width: min(440px, 88vw);
    height: min(440px, 88vw);
    border-radius: 50%;
    display: block;
    box-shadow:
        0 0 0 6px rgba(255,255,255,.06),
        0 0 60px rgba(124,58,237,.4),
        0 0 120px rgba(233,30,140,.2);
}
.btn-spin {
    height: 56px; padding: 0 3rem;
    border: none; border-radius: 14px;
    background: linear-gradient(135deg, var(--indigo) 0%, var(--purple) 55%, var(--pink) 100%);
    color: #fff; font-family: inherit;
    font-size: 1.1rem; font-weight: 800;
    letter-spacing: .04em; cursor: pointer;
    transition: opacity .2s, transform .15s, box-shadow .2s;
    box-shadow: 0 8px 28px rgba(67,56,202,.4);
    position: relative; overflow: hidden;
    width: 100%; max-width: 380px;
}
.btn-spin::before {
    content: '';
    position: absolute; inset: 0;
    background: linear-gradient(135deg, transparent, rgba(255,255,255,.1), transparent);
    transform: translateX(-100%); transition: transform .5s;
}
.btn-spin:hover:not(:disabled)::before { transform: translateX(100%); }
.btn-spin:hover:not(:disabled) { transform: translateY(-3px); box-shadow: 0 14px 36px rgba(67,56,202,.5); }
.btn-spin:disabled { opacity:.5; cursor:default; }

.btn-reload {
    background: transparent;
    border: 1px solid rgba(255,255,255,.18);
    color: rgba(255,255,255,.45);
    font-family: inherit; font-size: .78rem; font-weight: 500;
    padding: .4rem 1.2rem; border-radius: 8px;
    cursor: pointer; transition: all .15s;
}
.btn-reload:hover { border-color: rgba(255,255,255,.45); color: rgba(255,255,255,.8); }

/* ── Panel historial ── */
.history-panel {
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.1);
    border-radius: 20px;
    overflow: hidden;
    position: sticky;
    top: 1rem;
}
.history-head {
    padding: 1.1rem 1.4rem .9rem;
    border-bottom: 1px solid rgba(255,255,255,.08);
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.history-head h3 {
    font-size: .85rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .08em;
    color: rgba(255,255,255,.55);
}
.winners-count-pill {
    background: rgba(255,193,7,.15);
    border: 1px solid rgba(255,193,7,.3);
    color: var(--yellow);
    font-size: .7rem;
    font-weight: 700;
    padding: .2rem .6rem;
    border-radius: 20px;
    min-width: 24px;
    text-align: center;
}

.history-body {
    padding: .75rem;
    max-height: 380px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,.1) transparent;
}
.history-body::-webkit-scrollbar { width: 4px; }
.history-body::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 2px; }

.empty-state {
    text-align: center;
    padding: 2.5rem 1rem;
    color: rgba(255,255,255,.2);
    font-size: .82rem;
    line-height: 1.7;
}
.empty-state .icon { font-size: 2rem; margin-bottom: .5rem; display: block; }

.winner-item {
    display: flex;
    align-items: center;
    gap: .75rem;
    padding: .65rem .75rem;
    border-radius: 10px;
    margin-bottom: .4rem;
    background: rgba(255,255,255,.04);
    border: 1px solid rgba(255,255,255,.07);
    animation: slideIn .35s ease;
}
@keyframes slideIn {
    from { transform: translateX(20px); opacity: 0; }
    to   { transform: translateX(0);    opacity: 1; }
}
.winner-num {
    width: 26px; height: 26px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--indigo), var(--purple));
    display: flex; align-items: center; justify-content: center;
    font-size: .7rem; font-weight: 800;
    flex-shrink: 0;
}
.winner-info { flex: 1; min-width: 0; }
.winner-info .name {
    font-size: .85rem; font-weight: 700;
    color: #fff;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.winner-info .time {
    font-size: .68rem;
    color: rgba(255,255,255,.35);
    margin-top: .1rem;
}
.trophy-mini { font-size: 1rem; }

.history-foot {
    padding: .9rem 1rem;
    border-top: 1px solid rgba(255,255,255,.08);
    display: flex;
    flex-direction: column;
    gap: .5rem;
}

.btn-pdf {
    width: 100%; height: 40px;
    border: none; border-radius: 9px;
    background: linear-gradient(135deg, #059669, #0891b2);
    color: #fff; font-family: inherit;
    font-size: .82rem; font-weight: 700;
    cursor: pointer; display: flex;
    align-items: center; justify-content: center;
    gap: .45rem; transition: opacity .2s, transform .15s;
}
.btn-pdf:hover:not(:disabled) { transform: translateY(-1px); opacity: .9; }
.btn-pdf:disabled { opacity: .4; cursor: default; }

.btn-clear {
    width: 100%; height: 36px;
    border: 1px solid rgba(255,255,255,.12);
    background: transparent; border-radius: 9px;
    color: rgba(255,255,255,.35); font-family: inherit;
    font-size: .75rem; font-weight: 500;
    cursor: pointer; transition: all .15s;
}
.btn-clear:hover:not(:disabled) { border-color: rgba(255,255,255,.3); color: rgba(255,255,255,.6); }
.btn-clear:disabled { opacity: .3; cursor: default; }

/* ── Winner modal ── */
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
    box-shadow: 0 24px 64px rgba(67,56,202,.5), 0 0 120px rgba(124,58,237,.3);
    animation: popIn .4s cubic-bezier(.34,1.56,.64,1);
}
@keyframes popIn { from{transform:scale(.6);opacity:0} to{transform:scale(1);opacity:1} }
.trophy { font-size: 3.5rem; margin-bottom: 1rem; display: block; animation: trophyBounce 1s ease-in-out infinite alternate; }
@keyframes trophyBounce { from{transform:translateY(0) rotate(-5deg)} to{transform:translateY(-8px) rotate(5deg)} }
.winner-label { font-size: .85rem; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; color: var(--yellow); margin-bottom: .5rem; }
.winner-name { font-size: clamp(1.4rem, 5vw, 2rem); font-weight: 900; color: #fff; margin-bottom: 1.75rem; line-height: 1.2; word-break: break-word; }
.btn-close {
    background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
    color: #fff; font-family: inherit; font-size: .9rem; font-weight: 600;
    padding: .65rem 2rem; border-radius: 10px; cursor: pointer; transition: background .15s;
}
.btn-close:hover { background: rgba(255,255,255,.22); }

/* ── Confetti ── */
.confetti-piece {
    position: fixed; top: -20px;
    width: 10px; height: 10px; border-radius: 2px;
    opacity: 0; animation: confettiFall linear forwards; z-index: 99;
}
@keyframes confettiFall {
    0%   { transform: translateY(0) rotate(0deg);    opacity: 1; }
    100% { transform: translateY(100vh) rotate(720deg); opacity: 0; }
}

/* ── Responsive ── */
@media (max-width: 820px) {
    .main-layout {
        grid-template-columns: 1fr;
        padding: 0 .75rem 3rem;
    }
    .history-panel { position: static; }
    .total-badge { top: .75rem; right: .75rem; }
}
@media (max-width: 480px) {
    .header { padding: 1.75rem .75rem 1rem; }
    .winner-card { padding: 2rem 1.5rem; }
    .history-body { max-height: 260px; }
}

footer {
    position: relative; z-index: 1;
    text-align: center;
    padding: 1.5rem;
    color: rgba(255,255,255,.2);
    font-size: .75rem;
    border-top: 1px solid rgba(255,255,255,.05);
}
</style>
</head>
<body>

<div class="bg-canvas" aria-hidden="true"></div>

<!-- Contador fijo arriba derecha -->
<div class="total-badge">
    <span class="num" id="totalNum">–</span>
    <span class="lbl">participantes</span>
</div>

<!-- Header -->
<div class="header">
    <div class="date-badge"><span class="date-pip"></span>19 de septiembre de 2026</div>
    <h1>Ruleta de Participantes</h1>
    <p>Encuentro de Graduados 2026</p>
</div>

<!-- Layout principal -->
<div class="main-layout">

    <!-- Columna izquierda: ruleta -->
    <div class="wheel-area">
        <div class="wheel-wrap">
            <div class="pointer"></div>
            <canvas id="wheel" width="500" height="500"></canvas>
        </div>
        <button class="btn-spin" id="btnSpin" disabled>Cargando...</button>
        <button class="btn-reload" onclick="loadParticipants()">↻ Actualizar participantes</button>
    </div>

    <!-- Columna derecha: historial -->
    <div class="history-panel">
        <div class="history-head">
            <h3>Historial de ganadores</h3>
            <span class="winners-count-pill" id="winnersCount">0</span>
        </div>

        <div class="history-body" id="historyBody">
            <div class="empty-state" id="emptyState">
                <span class="icon">🎯</span>
                Gira la ruleta para ver<br>los ganadores aquí
            </div>
        </div>

        <div class="history-foot">
            <button class="btn-pdf" id="btnPdf" onclick="downloadPDF()" disabled>
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Descargar PDF
            </button>
            <button class="btn-clear" id="btnClear" onclick="clearHistory()" disabled>
                Limpiar historial
            </button>
        </div>
    </div>

</div>

<footer>Universidad de Ibagué &copy; 2026 &mdash; Encuentro de Graduados</footer>

<!-- Winner Modal -->
<div class="modal-overlay" id="modal" style="display:none;">
    <div class="winner-card">
        <span class="trophy">🏆</span>
        <div class="winner-label">¡Ganador!</div>
        <div class="winner-name" id="winnerName"></div>
        <button class="btn-close" onclick="closeModal()">Continuar</button>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
const COLORS = [
    '#e91e8c','#7c3aed','#4338ca','#0891b2',
    '#059669','#d97706','#dc2626','#0284c7',
    '#be185d','#065f46','#92400e','#1d4ed8',
    '#7e22ce','#0e7490','#166534','#b45309',
];

let participants = [];
let winners      = [];
let currentAngle = 0;
let spinning     = false;

const canvas = document.getElementById('wheel');
const ctx    = canvas.getContext('2d');
const W = canvas.width, H = canvas.height;
const CX = W/2, CY = H/2;
const R  = Math.min(CX, CY) - 8;

/* ── Cargar participantes ── */
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
        btn.textContent = '¡GIRAR!';
        btn.disabled    = false;
    } else {
        btn.textContent = n === 1 ? '¡Solo 1 participante!' : 'Sin participantes';
        btn.disabled    = true;
    }

    drawWheel(currentAngle);
}

/* ── Dibujar ruleta ── */
function drawWheel(angle) {
    ctx.clearRect(0, 0, W, H);
    const n = participants.length;

    if (n === 0) {
        ctx.beginPath();
        ctx.arc(CX, CY, R, 0, 2*Math.PI);
        ctx.fillStyle = '#1e293b';
        ctx.fill();
        ctx.fillStyle = 'rgba(255,255,255,.3)';
        ctx.font = 'bold 16px Inter, sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('Sin participantes', CX, CY);
        return;
    }

    const seg      = (2 * Math.PI) / n;
    const fontSize = n > 24 ? 9 : n > 16 ? 11 : n > 10 ? 13 : 15;
    const maxLen   = n > 16 ? 14 : 20;

    for (let i = 0; i < n; i++) {
        const start = angle - Math.PI/2 + i * seg;
        const end   = start + seg;
        const mid   = start + seg/2;

        ctx.beginPath();
        ctx.moveTo(CX, CY);
        ctx.arc(CX, CY, R, start, end);
        ctx.closePath();
        ctx.fillStyle = COLORS[i % COLORS.length];
        ctx.fill();
        ctx.strokeStyle = 'rgba(255,255,255,.25)';
        ctx.lineWidth = 1.5;
        ctx.stroke();

        ctx.save();
        ctx.translate(CX, CY);
        ctx.rotate(mid);
        ctx.textAlign    = 'right';
        ctx.textBaseline = 'middle';
        ctx.fillStyle    = '#fff';
        ctx.font         = `bold ${fontSize}px Inter, sans-serif`;
        ctx.shadowColor  = 'rgba(0,0,0,.6)';
        ctx.shadowBlur   = 4;
        let name = participants[i];
        if (name.length > maxLen) name = name.slice(0, maxLen - 1) + '…';
        ctx.fillText(name, R - 14, 0);
        ctx.restore();
    }

    // Aro exterior
    ctx.beginPath();
    ctx.arc(CX, CY, R, 0, 2*Math.PI);
    ctx.strokeStyle = 'rgba(255,255,255,.15)';
    ctx.lineWidth = 4;
    ctx.stroke();

    // Centro
    ctx.beginPath(); ctx.arc(CX, CY, 24, 0, 2*Math.PI);
    ctx.fillStyle = '#fff'; ctx.fill();
    ctx.beginPath(); ctx.arc(CX, CY, 17, 0, 2*Math.PI);
    ctx.fillStyle = '#1a3a6b'; ctx.fill();
}

/* ── Girar ── */
function spin() {
    if (spinning || participants.length < 2) return;
    spinning = true;

    const btn = document.getElementById('btnSpin');
    btn.disabled    = true;
    btn.textContent = 'Girando...';

    const rotations = 7 + Math.random() * 6;
    const extra     = Math.random() * 2 * Math.PI;
    const totalSpin = rotations * 2 * Math.PI + extra;
    const startAng  = currentAngle;
    const duration  = 5000 + Math.random() * 2000;
    const startTime = performance.now();

    function animate(now) {
        const t    = Math.min((now - startTime) / duration, 1);
        const ease = 1 - Math.pow(1 - t, 4);
        currentAngle = startAng + totalSpin * ease;
        drawWheel(currentAngle);

        if (t < 1) {
            requestAnimationFrame(animate);
        } else {
            currentAngle = startAng + totalSpin;
            drawWheel(currentAngle);
            spinning = false;

            const n   = participants.length;
            const seg = 2 * Math.PI / n;
            const a   = ((-currentAngle % (2*Math.PI)) + 2*Math.PI) % (2*Math.PI);
            const idx = Math.floor(a / seg) % n;

            setTimeout(() => {
                const winner = participants[idx];
                addWinner(winner);
                showWinner(winner);
                btn.disabled    = false;
                btn.textContent = '¡GIRAR OTRA VEZ!';
            }, 500);
        }
    }

    requestAnimationFrame(animate);
}

/* ── Historial ── */
function addWinner(name) {
    const now = new Date();
    const time = now.toLocaleTimeString('es-CO', { hour: '2-digit', minute: '2-digit' });
    winners.push({ name, time });

    const num   = winners.length;
    const body  = document.getElementById('historyBody');
    const empty = document.getElementById('emptyState');
    if (empty) empty.remove();

    const item = document.createElement('div');
    item.className = 'winner-item';
    item.innerHTML = `
        <div class="winner-num">${num}</div>
        <div class="winner-info">
            <div class="name">${name}</div>
            <div class="time">${time}</div>
        </div>
        <span class="trophy-mini">🏆</span>
    `;
    body.prepend(item);

    document.getElementById('winnersCount').textContent = num;
    document.getElementById('btnPdf').disabled   = false;
    document.getElementById('btnClear').disabled = false;
}

function clearHistory() {
    if (!winners.length) return;
    if (!confirm('¿Limpiar el historial de ganadores?')) return;
    winners = [];
    document.getElementById('historyBody').innerHTML =
        `<div class="empty-state" id="emptyState">
            <span class="icon">🎯</span>
            Gira la ruleta para ver<br>los ganadores aquí
        </div>`;
    document.getElementById('winnersCount').textContent = '0';
    document.getElementById('btnPdf').disabled   = true;
    document.getElementById('btnClear').disabled = true;
}

/* ── Descargar PDF ── */
function downloadPDF() {
    if (!winners.length) return;

    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });

    const pageW = doc.internal.pageSize.getWidth();

    // Encabezado
    doc.setFillColor(26, 58, 107);
    doc.rect(0, 0, pageW, 38, 'F');

    doc.setTextColor(255, 255, 255);
    doc.setFontSize(18);
    doc.setFont('helvetica', 'bold');
    doc.text('Encuentro de Graduados 2026', pageW/2, 16, { align: 'center' });

    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text('Universidad de Ibagué  •  19 de septiembre de 2026', pageW/2, 25, { align: 'center' });

    doc.setFontSize(11);
    doc.setFont('helvetica', 'bold');
    doc.text('Historial de Ganadores – Ruleta de Participantes', pageW/2, 33, { align: 'center' });

    // Fecha de generación
    doc.setTextColor(100, 100, 100);
    doc.setFontSize(8);
    doc.setFont('helvetica', 'normal');
    const now = new Date().toLocaleString('es-CO');
    doc.text(`Generado el ${now}`, pageW/2, 44, { align: 'center' });

    // Tabla de ganadores
    let y = 52;
    doc.setFontSize(10);

    winners.forEach((w, i) => {
        if (y > 270) {
            doc.addPage();
            y = 20;
        }

        const isEven = i % 2 === 0;
        if (isEven) {
            doc.setFillColor(240, 244, 255);
            doc.rect(14, y - 5, pageW - 28, 9, 'F');
        }

        doc.setTextColor(26, 58, 107);
        doc.setFont('helvetica', 'bold');
        doc.text(`${i + 1}.`, 18, y);

        doc.setTextColor(30, 30, 30);
        doc.setFont('helvetica', 'normal');
        doc.text(w.name, 28, y);

        doc.setTextColor(130, 130, 130);
        doc.setFontSize(8);
        doc.text(w.time, pageW - 20, y, { align: 'right' });
        doc.setFontSize(10);

        y += 10;
    });

    // Pie de página
    const pageCount = doc.internal.getNumberOfPages();
    for (let p = 1; p <= pageCount; p++) {
        doc.setPage(p);
        doc.setTextColor(180, 180, 180);
        doc.setFontSize(7);
        doc.text(
            `Página ${p} de ${pageCount}  •  Universidad de Ibagué`,
            pageW/2, 290, { align: 'center' }
        );
    }

    doc.save('ganadores-encuentro-graduados-2026.pdf');
}

/* ── Modal ── */
function showWinner(name) {
    document.getElementById('winnerName').textContent = name;
    document.getElementById('modal').style.display   = 'flex';
    launchConfetti();
}

function closeModal() {
    document.getElementById('modal').style.display = 'none';
    document.querySelectorAll('.confetti-piece').forEach(el => el.remove());
}

/* ── Confetti ── */
function launchConfetti() {
    const colors = ['#e91e8c','#ffc107','#00d4e0','#7c3aed','#ff6d00','#fff'];
    for (let i = 0; i < 80; i++) {
        const el = document.createElement('div');
        el.className = 'confetti-piece';
        el.style.left               = Math.random() * 100 + 'vw';
        el.style.background         = colors[Math.floor(Math.random() * colors.length)];
        el.style.width              = (6 + Math.random() * 8)  + 'px';
        el.style.height             = (6 + Math.random() * 12) + 'px';
        el.style.borderRadius       = Math.random() > .5 ? '50%' : '2px';
        el.style.animationDuration  = (2 + Math.random() * 3)  + 's';
        el.style.animationDelay     = (Math.random() * 1.5)    + 's';
        document.body.appendChild(el);
        el.addEventListener('animationend', () => el.remove());
    }
}

document.getElementById('btnSpin').addEventListener('click', spin);
loadParticipants();
</script>
</body>
</html>
