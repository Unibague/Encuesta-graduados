<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Juegos – Encuentro de Graduados 2026</title>
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
    background: #f8fafc;
    min-height: 100vh;
    color: #1e293b;
    overflow-x: hidden;
    -webkit-font-smoothing: antialiased;
}

/* ── Chispitas (fondo blanco) ── */
.bg-canvas { position: fixed; inset: 0; pointer-events: none; z-index: 0; overflow: hidden; }
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

.page { position: relative; z-index: 2; max-width: 720px; margin: 0 auto; padding: 0 1.25rem 4rem; }

/* ── Header ── */
.header-bar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 1.75rem 0 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    margin-bottom: 2rem;
    gap: 1rem;
}
.header-bar h1 { font-size: clamp(1.3rem, 3.5vw, 2rem); font-weight: 900; color: #1a3a6b; letter-spacing: -.02em; line-height: 1.15; }
.header-bar p { font-size: .78rem; color: #94a3b8; margin-top: .2rem; }
.total-box {
    text-align: right; flex-shrink: 0;
    background: #f1f5f9; border: 1px solid #e2e8f0;
    border-radius: 14px; padding: .7rem 1.2rem; min-width: 100px;
}
.total-box .lbl { font-size: .6rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #94a3b8; }
.total-box .num { font-size: 1.8rem; font-weight: 900; line-height: 1; color: #1a3a6b; }
.total-box .num .cupo-of { font-size: 1rem; font-weight: 700; color: #94a3b8; }
.total-box .cupo-mini-track { margin-top: .5rem; width: 100%; height: 5px; border-radius: 20px; background: #e2e8f0; overflow: hidden; }
.total-box .cupo-mini-fill {
    height: 100%; width: 0%; border-radius: 20px;
    background: linear-gradient(90deg, var(--cyan), var(--purple), var(--pink));
    transition: width .5s ease;
}
.total-box.agotado .cupo-mini-fill { background: linear-gradient(90deg, #fb923c, #ef4444); }

/* ── Card ── */
.card {
    background: #fff; border-radius: 20px;
    box-shadow: 0 8px 16px rgba(0,0,0,.06), 0 24px 48px rgba(100,116,139,.12);
    margin-bottom: 1.5rem;
}
.card-stripe {
    height: 4px; border-radius: 20px 20px 0 0;
    background: linear-gradient(90deg, #e91e8c, #7c3aed, #4338ca, #00d4e0, #1A3A6B);
    background-size: 200%;
    animation: stripeSlide 4s linear infinite;
}
@keyframes stripeSlide { 0% { background-position: 0% 0%; } 100% { background-position: 200% 0%; } }
.card-inner { padding: 1.75rem 1.75rem 2rem; }

/* ── Buscador ── */
.search-wrap { position: relative; }
.search-input {
    width: 100%; height: 50px;
    border: 1.5px solid #dde3ec; border-radius: 12px;
    padding: 0 1.1rem; font-size: .95rem; font-family: inherit;
    color: #1e293b; background: #fcfcfd;
    outline: none; transition: border-color .15s, box-shadow .15s;
}
.search-input:focus { border-color: var(--indigo); background: #fff; box-shadow: 0 0 0 3px rgba(67,56,202,.1); }

.search-dropdown {
    position: absolute; left: 0; right: 0; top: calc(100% + 10px);
    background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
    box-shadow: 0 20px 48px rgba(15,23,42,.28);
    max-height: 360px; overflow-y: auto; z-index: 20;
    display: none;
}
.search-dropdown.visible { display: block; }

.search-dropdown-head {
    position: sticky; top: 0;
    padding: .65rem 1.1rem; background: #f8fafc; border-bottom: 1px solid #e2e8f0;
    font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .06em;
    color: #94a3b8;
}

.search-item {
    padding: .65rem 1.1rem; font-size: .88rem; font-weight: 600; color: #334155;
    cursor: pointer; display: flex; align-items: center; justify-content: space-between; gap: .65rem;
    border-bottom: 1px solid #f1f5f9;
}
.search-item:last-child { border-bottom: none; }
.search-item:hover { background: #f5f3ff; }
.search-item .si-name { display: flex; align-items: center; gap: .65rem; min-width: 0; }
.search-item .si-avatar {
    width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg, var(--indigo), var(--purple));
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .64rem;
}
.search-item .si-label { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.search-item .si-score { font-size: .72rem; font-weight: 700; color: #94a3b8; flex-shrink: 0; }
.search-item .si-score.si-done { color: #16a34a; }
.search-empty { padding: 1.5rem 1.1rem; text-align: center; font-size: .82rem; color: #94a3b8; }

/* ── Jugador seleccionado ── */
.player-card { display: none; }
.player-card.visible { display: block; }

.player-head { display: flex; align-items: center; gap: .85rem; margin-bottom: 1.5rem; }
.player-avatar {
    width: 46px; height: 46px; border-radius: 50%; flex-shrink: 0;
    background: linear-gradient(135deg, var(--indigo), var(--purple));
    color: #fff; display: flex; align-items: center; justify-content: center;
    font-weight: 800; font-size: .95rem;
}
.player-name { font-size: 1.05rem; font-weight: 800; color: #1a3a6b; }
.player-id { font-size: .76rem; color: #94a3b8; }

.scores-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: .85rem; margin-bottom: 1.5rem; }
.score-field label {
    display: block; font-size: .72rem; font-weight: 700; text-transform: uppercase;
    letter-spacing: .05em; color: #94a3b8; margin-bottom: .4rem; text-align: center;
}
.score-field input {
    width: 100%; height: 56px; text-align: center;
    border: 1.5px solid #dde3ec; border-radius: 12px;
    font-size: 1.3rem; font-weight: 800; color: #1a3a6b; font-family: inherit;
    outline: none; transition: border-color .15s, box-shadow .15s, background .15s;
}
.score-field input:focus { border-color: var(--purple); box-shadow: 0 0 0 3px rgba(124,58,237,.12); }

.btn-save {
    width: 100%; height: 50px; border: none; border-radius: 12px;
    background: linear-gradient(135deg, var(--indigo) 0%, var(--purple) 55%, var(--pink) 100%);
    color: #fff; font-family: inherit; font-size: .92rem; font-weight: 800;
    letter-spacing: .01em; cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    transition: transform .15s, box-shadow .2s, opacity .2s;
    position: relative; overflow: hidden;
}
.btn-save:hover:not(:disabled) { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(67,56,202,.4); }
.btn-save:disabled { opacity: .6; cursor: default; }

.btn-change {
    width: 100%; height: 38px; border: 1.5px solid #e2e8f0; border-radius: 9px;
    background: transparent; color: #94a3b8; font-family: inherit;
    font-size: .8rem; font-weight: 600; cursor: pointer; margin-top: .7rem;
    transition: all .15s;
}
.btn-change:hover { border-color: #94a3b8; color: #64748b; }

.save-alert {
    margin-top: .85rem; padding: .7rem 1rem; border-radius: 8px;
    font-size: .82rem; display: none;
}
.save-alert.ok  { display: block; background: #f0fdf4; border: 1px solid #bbf7d0; color: #166534; }
.save-alert.err { display: block; background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; }

/* ── Historial de sesión ── */
.history-label {
    font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em;
    color: #94a3b8; margin-bottom: .85rem; display: block;
}
.winners-list { display: flex; flex-direction: column; gap: .5rem; margin-bottom: 1.5rem; }
.empty-state {
    text-align: center; padding: 1.75rem 1rem; color: #cbd5e1; font-size: .82rem;
    background: #f8fafc; border: 1px dashed #e2e8f0; border-radius: 14px;
}
.winner-row {
    display: flex; align-items: center; gap: 1rem;
    padding: .7rem 1.1rem; background: #fff; border: 1px solid #e2e8f0; border-radius: 12px;
    animation: slideIn .35s ease;
}
@keyframes slideIn { from{transform:translateX(-16px);opacity:0} to{transform:translateX(0);opacity:1} }
.wr-name { flex: 1; font-size: .88rem; font-weight: 700; color: #1e293b; }
.wr-total { font-size: .85rem; font-weight: 800; color: var(--purple); flex-shrink: 0; }

/* ── Ranking link ── */
.ranking-link {
    display: flex; align-items: center; justify-content: center; gap: .5rem;
    height: 52px; border-radius: 14px; text-decoration: none;
    background: linear-gradient(135deg, #1e1b4b 0%, #312e81 40%, #4c1d95 100%);
    color: #fff; font-weight: 800; font-size: .92rem;
    transition: transform .15s, box-shadow .2s;
    box-shadow: 0 8px 24px rgba(67,56,202,.3);
}
.ranking-link:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(67,56,202,.45); }

footer { position: relative; z-index: 1; text-align: center; padding: 1.5rem; color: #1a3a6b; font-size: .75rem; border-top: 1px solid #e2e8f0; }

/* ── Confetti ── */
.confetti-piece { position: fixed; top: -20px; opacity: 0; animation: confettiFall linear forwards; z-index: 99; }
@keyframes confettiFall {
    0%   { transform:translateY(0) rotate(0deg);    opacity:1; }
    100% { transform:translateY(100vh) rotate(720deg); opacity:0; }
}

@media (max-width: 500px) {
    .header-bar { flex-direction: column; align-items: flex-start; }
    .total-box { align-self: flex-end; }
    .card-inner { padding: 1.4rem 1.25rem 1.75rem; }
    .scores-grid { gap: .6rem; }
    .score-field input { height: 50px; font-size: 1.1rem; }
}
</style>
</head>
<body>

<div class="bg-canvas" aria-hidden="true"></div>

<div class="page">

    <div class="header-bar">
        <div>
            <h1>Puntajes de los Juegos</h1>
            <p>Encuentro de Graduados 2026 &nbsp;•&nbsp; 19 de septiembre de 2026</p>
        </div>
        <div class="total-box" id="totalBox">
            <div class="lbl">Cupo</div>
            <div class="num"><span id="totalNum">–</span><span class="cupo-of">/100</span></div>
            <div class="cupo-mini-track"><div class="cupo-mini-fill" id="cupoMiniFill"></div></div>
        </div>
    </div>

    <!-- Buscador -->
    <div class="card">
        <div class="card-stripe"></div>
        <div class="card-inner">

            <div class="search-wrap" id="searchWrap">
                <input type="text" class="search-input" id="searchInput"
                       placeholder="Escribe un nombre o haz clic para ver la lista completa…" autocomplete="off">
                <div class="search-dropdown" id="searchDropdown"></div>
            </div>

            <!-- Jugador seleccionado -->
            <div class="player-card" id="playerCard" style="margin-top:1.5rem;">
                <div class="player-head">
                    <div class="player-avatar" id="playerAvatar">–</div>
                    <div>
                        <div class="player-name" id="playerName">–</div>
                        <div class="player-id" id="playerId">Graduado confirmado</div>
                    </div>
                </div>

                <div class="scores-grid">
                    <div class="score-field">
                        <label>Juego 1</label>
                        <input type="number" inputmode="numeric" min="0" max="100000" id="juego1" placeholder="0">
                    </div>
                    <div class="score-field">
                        <label>Juego 2</label>
                        <input type="number" inputmode="numeric" min="0" max="100000" id="juego2" placeholder="0">
                    </div>
                    <div class="score-field">
                        <label>Juego 3</label>
                        <input type="number" inputmode="numeric" min="0" max="100000" id="juego3" placeholder="0">
                    </div>
                </div>

                <button class="btn-save" id="btnSave">Guardar puntaje</button>
                <button class="btn-change" id="btnChange">Buscar otro graduado</button>

                <div class="save-alert" id="saveAlert"></div>
            </div>

        </div>
    </div>

    <!-- Historial de sesión -->
    <span class="history-label">Puntajes guardados en esta sesión</span>
    <div class="winners-list" id="winnersList">
        <div class="empty-state" id="emptyState">Aún no has guardado ningún puntaje.</div>
    </div>

    <a class="ranking-link" href="/ranking-juegos.php" target="_blank" rel="noopener">🏆 Ver ranking en pantalla completa</a>

</div>

<footer>Universidad de Ibagué &copy; 2026 &mdash; Encuentro de Graduados</footer>

<script>
const $ = id => document.getElementById(id);

let participantes = [];
let seleccionado   = null;
let guardadosSesion = 0;

const CUPO_CAPACIDAD = 100;

async function cargarParticipantes() {
    try {
        const res  = await fetch('/api/participantes-juegos.php');
        const data = await res.json();
        participantes = data.participantes || [];
    } catch (e) {
        participantes = [];
    }

    $('totalNum').textContent = participantes.length;

    const pct   = Math.min((participantes.length / CUPO_CAPACIDAD) * 100, 100);
    const lleno = participantes.length >= CUPO_CAPACIDAD;
    $('cupoMiniFill').style.width = pct + '%';
    $('totalBox').classList.toggle('agotado', lleno);
}

function iniciales(nombreCompleto) {
    const partes = (nombreCompleto || '').trim().split(/\s+/).filter(Boolean);
    const a = (partes[0] || '').charAt(0);
    const b = (partes[1] || '').charAt(0);
    return (a + b).toUpperCase() || '?';
}

function totalDe(p) {
    return (p.juego1 || 0) + (p.juego2 || 0) + (p.juego3 || 0);
}

function abrirDropdown() {
    $('searchDropdown').classList.add('visible');
}

function cerrarDropdown() {
    $('searchDropdown').classList.remove('visible');
}

function renderDropdown(filtro) {
    const dd = $('searchDropdown');
    const q  = filtro.trim().toLowerCase();

    if (!participantes.length) {
        cerrarDropdown();
        dd.innerHTML = '';
        return;
    }

    // Sin texto: se muestra la lista completa de confirmados para elegir directamente.
    const resultados = q === ''
        ? participantes
        : participantes.filter(p => p.nombre.toLowerCase().includes(q));

    if (!resultados.length) {
        dd.innerHTML = '<div class="search-empty">No se encontraron graduados confirmados con ese nombre.</div>';
        abrirDropdown();
        return;
    }

    const encabezado = q === ''
        ? `${participantes.length} graduados confirmados`
        : `${resultados.length} resultado${resultados.length === 1 ? '' : 's'}`;

    const filas = resultados.map(p => {
        const jugados = [p.juego1, p.juego2, p.juego3].filter(v => v !== null).length;
        const tag = jugados > 0
            ? `<span class="si-score si-done">${totalDe(p)} pts · ${jugados}/3</span>`
            : `<span class="si-score">Sin puntaje</span>`;
        return `<div class="search-item" data-idx="${participantes.indexOf(p)}">
            <span class="si-name"><span class="si-avatar">${iniciales(p.nombre)}</span><span class="si-label">${p.nombre}</span></span>${tag}
        </div>`;
    }).join('');

    dd.innerHTML = `<div class="search-dropdown-head">${encabezado}</div>${filas}`;

    abrirDropdown();

    dd.querySelectorAll('.search-item').forEach(el => {
        el.addEventListener('click', () => seleccionar(participantes[parseInt(el.dataset.idx, 10)]));
    });
}

function seleccionar(p) {
    seleccionado = p;
    $('searchWrap').style.display = 'none';
    $('playerCard').classList.add('visible');

    $('playerAvatar').textContent = iniciales(p.nombre);
    $('playerName').textContent   = p.nombre;
    $('playerId').textContent     = 'Graduado confirmado';

    $('juego1').value = p.juego1 ?? '';
    $('juego2').value = p.juego2 ?? '';
    $('juego3').value = p.juego3 ?? '';

    $('saveAlert').style.display = 'none';
    $('searchInput').value = '';
    cerrarDropdown();
}

function volverABuscar() {
    seleccionado = null;
    $('searchWrap').style.display = 'block';
    $('playerCard').classList.remove('visible');
    $('searchInput').focus();
}

$('searchInput').addEventListener('input', e => renderDropdown(e.target.value));
$('searchInput').addEventListener('focus', e => renderDropdown(e.target.value));
$('searchInput').addEventListener('click', e => renderDropdown(e.target.value));
$('btnChange').addEventListener('click', volverABuscar);

document.addEventListener('click', e => {
    if (!$('searchWrap').contains(e.target)) {
        cerrarDropdown();
    }
});

$('btnSave').addEventListener('click', async () => {
    if (!seleccionado) return;

    const btn = $('btnSave');
    const alert = $('saveAlert');
    btn.disabled = true;
    btn.textContent = 'Guardando...';
    alert.style.display = 'none';

    const payload = {
        nombre: seleccionado.nombre,
        juego1: $('juego1').value === '' ? null : $('juego1').value,
        juego2: $('juego2').value === '' ? null : $('juego2').value,
        juego3: $('juego3').value === '' ? null : $('juego3').value,
    };

    try {
        const res  = await fetch('/api/save-puntaje-juego.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload),
        });
        const json = await res.json();
        if (!res.ok || json.error) throw new Error(json.message || 'Error al guardar el puntaje.');

        seleccionado.juego1 = payload.juego1 !== null ? parseInt(payload.juego1, 10) : null;
        seleccionado.juego2 = payload.juego2 !== null ? parseInt(payload.juego2, 10) : null;
        seleccionado.juego3 = payload.juego3 !== null ? parseInt(payload.juego3, 10) : null;

        alert.textContent = 'Puntaje guardado correctamente.';
        alert.className = 'save-alert ok';
        agregarAHistorial(seleccionado.nombre, json.total);
        launchConfetti();

        setTimeout(volverABuscar, 900);
    } catch (err) {
        alert.textContent = err.message;
        alert.className = 'save-alert err';
    } finally {
        btn.disabled = false;
        btn.textContent = 'Guardar puntaje';
    }
});

function agregarAHistorial(nombre, total) {
    const list  = $('winnersList');
    const empty = $('emptyState');
    if (empty) empty.remove();

    guardadosSesion++;
    const row = document.createElement('div');
    row.className = 'winner-row';
    row.innerHTML = `<div class="wr-name">${nombre}</div><div class="wr-total">${total} pts</div>`;
    list.prepend(row);
}

function launchConfetti() {
    const colors = ['#e91e8c','#1A3A6B','#00d4e0','#7c3aed','#ff6d00','#fff'];
    for (let i = 0; i < 50; i++) {
        const el = document.createElement('div');
        el.className = 'confetti-piece';
        el.style.cssText = `
            left:${Math.random()*100}vw;
            background:${colors[Math.floor(Math.random()*colors.length)]};
            width:${6+Math.random()*8}px;
            height:${6+Math.random()*12}px;
            border-radius:${Math.random()>.5?'50%':'2px'};
            animation-duration:${1.5+Math.random()*2}s;
            animation-delay:${Math.random()*.6}s;
        `;
        document.body.appendChild(el);
        el.addEventListener('animationend', () => el.remove());
    }
}

cargarParticipantes();
</script>
</body>
</html>
