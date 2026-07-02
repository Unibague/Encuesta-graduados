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
    --purple: #7c3aed;
    --indigo: #4338ca;
    --navy:   #07111f;
}

body {
    font-family: 'Inter', system-ui, sans-serif;
    background: #f8fafc;
    min-height: 100vh;
    color: #1e293b;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
}

/* ── Chispitas (fondo blanco) ── */
.bg-canvas {
    position: fixed; inset: 0;
    pointer-events: none; z-index: 0; overflow: hidden;
}
.bg-canvas::after {
    content: '';
    position: absolute;
    width: 4px; height: 4px; border-radius: 50%;
    background: transparent;
    box-shadow:
        8vw  12vh 0 2px rgba(251,182,206,.85),
        22vw  5vh 0 3px rgba(196,181,253,.80),
        38vw 18vh 0 2px rgba(147,197,253,.85),
        55vw  8vh 0 3px rgba(110,231,183,.80),
        70vw 14vh 0 2px rgba(253,211,116,.85),
        85vw  6vh 0 3px rgba(253,186,116,.80),
        92vw 22vh 0 2px rgba(251,182,206,.75),
        5vw  40vh 0 3px rgba(216,180,254,.80),
        18vw 55vh 0 2px rgba(167,243,208,.85),
        32vw 70vh 0 3px rgba(147,197,253,.75),
        48vw 48vh 0 2px rgba(251,182,206,.80),
        62vw 62vh 0 3px rgba(196,181,253,.75),
        76vw 45vh 0 2px rgba(110,231,183,.80),
        90vw 58vh 0 3px rgba(253,211,116,.75),
        12vw 80vh 0 2px rgba(253,186,116,.80),
        28vw 88vh 0 3px rgba(216,180,254,.75),
        44vw 82vh 0 2px rgba(167,243,208,.80),
        60vw 78vh 0 3px rgba(147,197,253,.80),
        78vw 85vh 0 2px rgba(251,182,206,.75),
        95vw 72vh 0 3px rgba(196,181,253,.80);
    animation: twinkle 5s ease-in-out infinite alternate;
}
@keyframes twinkle {
    0%  { opacity:.5; transform:scale(1);   }
    50% { opacity:1;  transform:scale(1.4); }
    100%{ opacity:.4; transform:scale(.8);  }
}

/* ── Wrapper ── */
.page {
    position: relative; z-index: 1;
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
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 2.5rem;
    gap: 1rem;
}
.header-bar h1 {
    font-size: clamp(1.3rem, 3.5vw, 2rem);
    font-weight: 900;
    color: #1a3a6b;
    letter-spacing: -.02em;
    line-height: 1.15;
}
.header-bar p {
    font-size: .78rem;
    color: #94a3b8;
    margin-top: .2rem;
}
.total-box {
    text-align: right;
    flex-shrink: 0;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: .7rem 1.2rem;
    min-width: 110px;
}
.total-box .lbl {
    font-size: .62rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .1em;
    color: #94a3b8;
}
.total-box .num {
    font-size: 2rem;
    font-weight: 900;
    line-height: 1;
    color: #1a3a6b;
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
    border-top: 28px solid var(--yellow);
    filter: drop-shadow(0 2px 6px rgba(255,193,7,.7));
}
canvas#wheel {
    width: min(460px, 86vw);
    height: min(460px, 86vw);
    border-radius: 50%; display: block;
    box-shadow:
        0 0 0 6px #fff,
        0 4px 32px rgba(100,116,139,.18),
        0 12px 60px rgba(196,181,253,.25);
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
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
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
    color: #16a34a; margin-bottom: .5rem;
}
.current-winner .cw-name {
    font-size: clamp(1.3rem, 4vw, 1.9rem);
    font-weight: 900; color: #166534; line-height: 1.2;
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
    color: #94a3b8;
}
.winners-pill {
    background: rgba(255,193,7,.15);
    border: 1px solid rgba(255,193,7,.3);
    color: var(--yellow);
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
    border: 1px solid #e2e8f0;
    background: transparent; border-radius: 8px;
    color: #94a3b8; font-family: inherit;
    font-size: .75rem; font-weight: 500;
    cursor: pointer; transition: all .15s;
}
.btn-clear:hover:not(:disabled) { border-color: #94a3b8; color: #475569; }
.btn-clear:disabled { opacity: .3; cursor: default; }

/* Lista de ganadores */
.winners-list { display: flex; flex-direction: column; gap: .5rem; }

.empty-state {
    text-align: center;
    padding: 2.5rem 1rem;
    color: #cbd5e1;
    font-size: .82rem; line-height: 1.8;
    background: #f8fafc;
    border: 1px dashed #e2e8f0;
    border-radius: 14px;
}
.empty-state .icon { display: none; }

.winner-row {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: .85rem 1.25rem;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    animation: slideIn .35s ease;
}
@keyframes slideIn { from{transform:translateX(-16px);opacity:0} to{transform:translateX(0);opacity:1} }
.winner-row:first-child {
    background: #fffbeb;
    border-color: #fde68a;
}
.wr-num {
    width: 30px; height: 30px; border-radius: 50%;
    background: linear-gradient(135deg, var(--indigo), var(--purple));
    display: flex; align-items: center; justify-content: center;
    font-size: .72rem; font-weight: 800; flex-shrink: 0;
}
.winner-row:first-child .wr-num {
    background: linear-gradient(135deg, #d97706, #f59e0b);
}
.wr-name { flex: 1; font-size: .9rem; font-weight: 700; color: #1e293b; }
.wr-time { font-size: .72rem; color: #94a3b8; flex-shrink: 0; }
.wr-trophy { font-size: 1.1rem; }

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
    color: #1a3a6b; font-size: .75rem;
    border-top: 1px solid #e2e8f0;
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
        <div>
            <h1>Ruleta de Participantes</h1>
            <p>Encuentro de Graduados 2026 &nbsp;•&nbsp; 19 de septiembre de 2026</p>
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
        <div class="tag">🏆 &nbsp;Ganador actual</div>
        <div class="cw-name" id="currentWinnerName"></div>
    </div>

    <!-- Historial abajo -->
    <div class="history-section">
        <div class="history-head">
            <div class="history-head-left">
                <span class="history-label">Historial</span>
                <span class="winners-pill" id="winnersCount">0</span>
                <span style="font-size:.72rem;color:#94a3b8;font-weight:500;">ganadores anteriores</span>
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
const COLORS = [
    '#fbb6ce','#c4b5fd','#93c5fd','#6ee7b7',
    '#fcd34d','#fdba74','#fca5a5','#7dd3fc',
    '#d8b4fe','#a7f3d0','#fde68a','#f0abfc',
    '#bae6fd','#bbf7d0','#fed7aa','#e9d5ff',
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
        ctx.fillStyle = '#f5f0e6'; ctx.fill();
        ctx.fillStyle = 'rgba(26,58,107,.4)';
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
        ctx.fillStyle = '#374151';
        ctx.font = `bold ${fontSize}px Inter, sans-serif`;
        ctx.shadowColor = 'rgba(255,255,255,.8)'; ctx.shadowBlur = 3;
        let name = participants[i];
        if (name.length > maxLen) name = name.slice(0, maxLen-1) + '…';
        ctx.fillText(name, R - 14, 0);
        ctx.restore();
    }

    ctx.beginPath(); ctx.arc(CX, CY, R, 0, 2*Math.PI);
    ctx.strokeStyle = 'rgba(100,116,139,.2)'; ctx.lineWidth = 4; ctx.stroke();

    ctx.beginPath(); ctx.arc(CX, CY, 24, 0, 2*Math.PI); ctx.fillStyle = '#fff'; ctx.fill();
    ctx.beginPath(); ctx.arc(CX, CY, 17, 0, 2*Math.PI); ctx.fillStyle = '#1a3a6b'; ctx.fill();
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
        <span class="wr-trophy">🏆</span>
    `;
    list.prepend(row);

    document.getElementById('winnersCount').textContent = winners.length;
    document.getElementById('btnPdf').disabled   = false;
    document.getElementById('btnClear').disabled = false;
}

function clearHistory() {
    if (!winners.length || !confirm('¿Limpiar el historial de ganadores?')) return;
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
    const colors = ['#e91e8c','#ffc107','#00d4e0','#7c3aed','#ff6d00','#fff'];
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
