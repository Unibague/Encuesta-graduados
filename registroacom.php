<?php
// Encuesta pública de consentimiento y datos personales.
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro acompañante - Universidad de Ibagué</title>
    <link rel="icon" type="image/svg+xml" href="/images/favicon.svg">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1A3A6B;
            --primary-dark: #0F2747;
            --primary-light: #E8EEF7;
            --success: #16a672;
            --danger: #ef4444;
            --danger-light: #fef2f2;
            --text: #1f2333;
            --text-muted: #7c8093;
            --border: #e6e5f1;
            --shadow: 0 20px 45px -20px rgba(26, 58, 107, 0.35);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            min-height: 100vh;
            font-family: 'Manrope', sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 12% 8%, rgba(26, 58, 107, .18), transparent 40%),
                radial-gradient(circle at 88% 92%, rgba(22, 166, 114, .14), transparent 42%),
                #f6f6fb;
        }

        .survey-shell {
            display: flex;
            justify-content: center;
            min-height: 100vh;
            padding: 40px 16px 60px;
        }

        .survey-container { width: 100%; max-width: 680px; }

        .survey-intro { margin-bottom: 28px; text-align: center; }
        .survey-intro h1 { font-size: 1.9rem; font-weight: 800; margin-bottom: 8px; }
        .survey-intro p { color: var(--text-muted); font-size: .95rem; }

        .progress-container { margin-bottom: 22px; }
        .progress-row { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .section-pill {
            background: var(--primary-light);
            border-radius: 999px;
            color: var(--primary-dark);
            font-size: 12.5px;
            font-weight: 700;
            padding: 6px 12px;
        }
        .progress-meta { color: var(--text-muted); font-size: 12.5px; font-weight: 600; }
        .progress-bg { background: #e9e8f5; border-radius: 999px; height: 8px; overflow: hidden; }
        .progress-fill { background: var(--primary); height: 100%; transition: width .3s ease; }

        .survey-card {
            background: #fff;
            border: 1px solid rgba(26, 58, 107, .12);
            border-radius: 26px;
            box-shadow: var(--shadow);
            min-height: 360px;
            padding: 36px 32px 28px;
        }

        .section-title { font-size: 1.35rem; font-weight: 800; margin-bottom: 6px; }
        .section-subtitle { color: var(--text-muted); font-size: .9rem; margin-bottom: 22px; }
        .field { margin-bottom: 22px; }
        label { display: block; font-size: .98rem; font-weight: 700; margin-bottom: 8px; }
        input { width: 100%; padding: 13px 15px; border: 2px solid var(--border); border-radius: 14px; font: inherit; }
        input:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(26, 58, 107, .18); outline: none; }
        .consent-options { display: grid; gap: 12px; }
        .consent-option { border: 2px solid var(--border); border-radius: 14px; cursor: pointer; padding: 14px 16px; }
        .consent-option:has(input:checked) { background: var(--primary-light); border-color: var(--primary); }
        .consent-option input { margin-right: 8px; width: auto; }
        .nav-row { display: flex; justify-content: space-between; gap: 12px; margin-top: 28px; }
        button { border: 0; border-radius: 13px; cursor: pointer; font: 700 14px inherit; padding: 13px 22px; }
        .btn-primary { background: var(--primary); color: #fff; }
        .btn-secondary { background: #eef1f7; color: var(--primary-dark); }
        .hidden { display: none; }
        .message { border-radius: 12px; margin-bottom: 18px; padding: 14px 16px; }
        .message.error { background: var(--danger-light); color: #b91c1c; }
        .message.success { background: #e4f7f0; color: #087f5b; }
        .summary { background: #f6f8fc; border-radius: 12px; padding: 16px; }
        .summary p { margin-bottom: 8px; }
        .summary p:last-child { margin-bottom: 0; }
        .thankyou-wrap { padding: 30px 0 10px; text-align: center; }
        .thankyou-icon {
            align-items: center;
            background: #e4f7f0;
            border-radius: 50%;
            color: var(--success);
            display: flex;
            font-size: 36px;
            height: 76px;
            justify-content: center;
            margin: 0 auto 18px;
            width: 76px;
        }
        .thankyou-wrap h2 { font-size: 1.5rem; font-weight: 800; margin-bottom: 8px; }
        .thankyou-wrap p { color: var(--text-muted); font-size: .95rem; margin: 0 auto; max-width: 380px; }

        @media (max-width: 560px) {
            .survey-shell { padding: 24px 12px 36px; }
            .survey-card { border-radius: 20px; padding: 28px 18px 22px; }
        }
    </style>
</head>
<body>
    <main class="survey-shell">
        <div class="survey-container">
            <header class="survey-intro">
                <h1>Registro acompañante</h1>
                <p>Completa los datos solicitados para continuar.</p>
            </header>

            <div id="message" class="message hidden" role="alert"></div>

            <div class="progress-container">
                <div class="progress-row">
                    <span class="section-pill" id="sectionPill">Consentimiento</span>
                    <span class="progress-meta" id="progressMeta">Sección 1 de 2</span>
                </div>
                <div class="progress-bg"><div class="progress-fill" id="progressFill" style="width: 0%"></div></div>
            </div>

            <section class="survey-card">
                <div id="consentSection">
                    <h2 class="section-title">Consentimiento</h2>
                    <p class="section-subtitle">Autoriza el tratamiento de tus datos personales.</p>
                    <div class="field">
                        <label>¿Autoriza el tratamiento de sus datos personales?</label>
                        <div class="consent-options">
                            <label class="consent-option"><input type="radio" name="consentimiento" value="Sí"> Sí, autorizo</label>
                            <label class="consent-option"><input type="radio" name="consentimiento" value="No"> No autorizo</label>
                        </div>
                    </div>
                    <div class="nav-row">
                        <span></span>
                        <button type="button" class="btn-primary" id="continueButton">Continuar</button>
                    </div>
                </div>

                <div id="personalSection" class="hidden">
                    <h2 class="section-title">Datos personales</h2>
                    <p class="section-subtitle">Ingresa tu nombre, apellido y cédula.</p>
                    <div class="field">
                        <label for="nombres">Nombre</label>
                        <input id="nombres" name="nombres" type="text" autocomplete="given-name" required>
                    </div>
                    <div class="field">
                        <label for="apellidos">Apellido</label>
                        <input id="apellidos" name="apellidos" type="text" autocomplete="family-name" required>
                    </div>
                    <div class="field">
                        <label for="cedula">Cédula</label>
                        <input id="cedula" name="cedula" type="text" inputmode="numeric" required>
                    </div>
                    <div class="nav-row">
                        <button type="button" class="btn-secondary" id="backButton">Atrás</button>
                        <button type="button" class="btn-primary" id="submitButton">Terminar registro</button>
                    </div>
                </div>

                <div id="successSection" class="hidden">
                    <div class="thankyou-wrap">
                        <div class="thankyou-icon">✓</div>
                        <h2>¡Gracias por responder!</h2>
                        <p>Tus respuestas fueron registradas correctamente. Agradecemos el tiempo que dedicaste a esta encuesta.</p>
                    </div>
                </div>
            </section>
        </div>
    </main>

    <script>
        const message = document.getElementById('message');
        const consentSection = document.getElementById('consentSection');
        const personalSection = document.getElementById('personalSection');
        const successSection = document.getElementById('successSection');
        const sectionPill = document.getElementById('sectionPill');
        const progressMeta = document.getElementById('progressMeta');
        const progressFill = document.getElementById('progressFill');

        function showMessage(text, type = 'error') {
            message.textContent = text;
            message.className = `message ${type}`;
        }

        function updateProgress(section) {
            const personal = section === 'personal';
            sectionPill.textContent = personal ? 'Datos personales' : 'Consentimiento';
            progressMeta.textContent = personal ? 'Sección 2 de 2' : 'Sección 1 de 2';
            progressFill.style.width = personal ? '50%' : '0%';
        }

        document.getElementById('continueButton').addEventListener('click', function () {
            const consent = document.querySelector('input[name="consentimiento"]:checked');
            if (!consent) {
                showMessage('Selecciona una opción para continuar.');
                return;
            }
            if (consent.value === 'No') {
                showMessage('Para continuar debes autorizar el tratamiento de tus datos.');
                return;
            }
            message.className = 'message hidden';
            consentSection.classList.add('hidden');
            personalSection.classList.remove('hidden');
            updateProgress('personal');
        });

        document.getElementById('backButton').addEventListener('click', function () {
            personalSection.classList.add('hidden');
            consentSection.classList.remove('hidden');
            updateProgress('consent');
        });

        document.getElementById('submitButton').addEventListener('click', async function () {
            const nombres = document.getElementById('nombres').value.trim();
            const apellidos = document.getElementById('apellidos').value.trim();
            const cedula = document.getElementById('cedula').value.trim();
            const consentimiento = document.querySelector('input[name="consentimiento"]:checked')?.value || '';

            if (!nombres || !apellidos || !cedula) {
                showMessage('Completa todos los datos personales.');
                return;
            }

            this.disabled = true;
            try {
                const response = await fetch('/api/save-registroacom.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ consentimiento, nombres, apellidos, cedula })
                });
                const result = await response.json();
                if (!response.ok || !result.success) throw new Error(result.message || 'No fue posible guardar el registro.');

                personalSection.classList.add('hidden');
                successSection.classList.remove('hidden');
                sectionPill.textContent = 'Finalizado';
                progressMeta.textContent = 'Registro completado';
                progressFill.style.width = '100%';
                message.className = 'message hidden';
            } catch (error) {
                showMessage(error.message);
                this.disabled = false;
            }
        });
    </script>
</body>
</html>
