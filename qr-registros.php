<?php
// Vista pública con accesos QR. Las URL se forman con el mismo host desde el
// que se abre esta página, por lo que funcionan en local y en producción.
$isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
$scheme = $isHttps ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
$basePath = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
$baseUrl = $scheme . '://' . $host . ($basePath === '' ? '' : $basePath);

$forms = [
    [
        'title' => 'Registro de graduados',
        'description' => 'Actualiza tu información como graduado de la Universidad de Ibagué.',
        'path' => 'registrograduados.php',
        'accent' => '#1a3a6b',
    ],
    [
        'title' => 'Registro acompañantes',
        'description' => 'Autoriza el tratamiento de datos y completa tu información personal.',
        'path' => 'registroacom.php',
        'accent' => '#167d68',
    ],
];

foreach ($forms as &$form) {
    $form['url'] = $baseUrl . '/' . $form['path'];
}
unset($form);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Códigos QR de registro - Universidad de Ibagué</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1a3a6b;
            --primary-dark: #0f2747;
            --text: #1f2333;
            --muted: #71768a;
            --border: #e2e7f0;
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Manrope', Arial, sans-serif;
            color: var(--text);
            background:
                radial-gradient(circle at 10% 5%, rgba(26, 58, 107, .17), transparent 36%),
                radial-gradient(circle at 90% 95%, rgba(22, 125, 104, .14), transparent 38%),
                #f6f7fb;
        }

        .page {
            width: min(1040px, calc(100% - 32px));
            margin: 0 auto;
            padding: 52px 0 64px;
        }

        .header { text-align: center; margin-bottom: 34px; }
        .brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 18px;
            color: var(--primary);
            font-size: .82rem;
            font-weight: 800;
            letter-spacing: .08em;
            text-transform: uppercase;
        }
        .brand-mark {
            display: grid;
            place-items: center;
            width: 34px;
            height: 34px;
            border-radius: 10px;
            background: var(--primary);
            color: #fff;
            font-size: .72rem;
        }
        h1 { margin: 0 0 8px; color: #151c2e; font-size: clamp(1.8rem, 4vw, 2.45rem); }
        .header p { margin: 0; color: var(--muted); }

        .cards {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }

        .qr-card {
            --accent: var(--primary);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px;
            background: rgba(255, 255, 255, .96);
            border: 1px solid rgba(26, 58, 107, .12);
            border-top: 5px solid var(--accent);
            border-radius: 24px;
            box-shadow: 0 22px 50px -28px rgba(18, 40, 75, .45);
            text-align: center;
        }

        .qr-card h2 { margin: 0 0 8px; color: var(--primary-dark); font-size: 1.28rem; }
        .description { min-height: 48px; margin: 0 0 22px; color: var(--muted); font-size: .9rem; line-height: 1.55; }
        .qr-box {
            display: grid;
            place-items: center;
            width: 250px;
            height: 250px;
            padding: 12px;
            background: #fff;
            border: 2px solid var(--border);
            border-radius: 18px;
        }
        .qr-box canvas, .qr-box img { display: block; width: 222px !important; height: 222px !important; }
        .url {
            width: 100%;
            margin: 18px 0;
            padding: 10px 12px;
            overflow-wrap: anywhere;
            border-radius: 10px;
            background: #f5f7fb;
            color: #657087;
            font-size: .75rem;
            line-height: 1.4;
        }
        .actions { display: flex; gap: 10px; margin-top: auto; }
        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 42px;
            padding: 10px 17px;
            border: 0;
            border-radius: 11px;
            background: var(--accent);
            color: #fff;
            cursor: pointer;
            font: 700 .82rem 'Manrope', sans-serif;
            text-decoration: none;
        }
        .button.secondary { background: #eaf0f7; color: var(--primary-dark); }
        .button:hover { filter: brightness(.94); }
        .status { margin-top: 24px; color: var(--muted); text-align: center; font-size: .8rem; }
        .status strong { color: var(--primary-dark); }
        .error { color: #b42318; font-size: .82rem; line-height: 1.45; }

        @media (max-width: 720px) {
            .page { padding-top: 32px; }
            .cards { grid-template-columns: 1fr; }
            .description { min-height: 0; }
            .qr-card { padding: 26px 20px; }
        }

        @media print {
            body { background: #fff; }
            .page { width: 100%; padding: 12mm; }
            .header { margin-bottom: 8mm; }
            .qr-card { break-inside: avoid; box-shadow: none; }
            .actions, .status { display: none; }
        }
    </style>
</head>
<body>
    <main class="page">
        <header class="header">
            <div class="brand"><span class="brand-mark">UI</span> Universidad de Ibagué</div>
            <h1>Formularios de registro</h1>
            <p>Escanea el código correspondiente para acceder al formulario.</p>
        </header>

        <section class="cards" aria-label="Códigos QR de los formularios">
            <?php foreach ($forms as $index => $form): ?>
                <article class="qr-card" style="--accent: <?= htmlspecialchars($form['accent'], ENT_QUOTES, 'UTF-8') ?>">
                    <h2><?= htmlspecialchars($form['title'], ENT_QUOTES, 'UTF-8') ?></h2>
                    <p class="description"><?= htmlspecialchars($form['description'], ENT_QUOTES, 'UTF-8') ?></p>
                    <div
                        class="qr-box"
                        id="qr-<?= $index ?>"
                        data-url="<?= htmlspecialchars($form['url'], ENT_QUOTES, 'UTF-8') ?>"
                        aria-label="Código QR para <?= htmlspecialchars($form['title'], ENT_QUOTES, 'UTF-8') ?>"
                    ></div>
                    <div class="url"><?= htmlspecialchars($form['url'], ENT_QUOTES, 'UTF-8') ?></div>
                    <div class="actions">
                        <a class="button" href="<?= htmlspecialchars($form['url'], ENT_QUOTES, 'UTF-8') ?>">Abrir formulario</a>
                        <button class="button secondary" type="button" data-download="qr-<?= $index ?>" data-name="<?= htmlspecialchars(pathinfo($form['path'], PATHINFO_FILENAME), ENT_QUOTES, 'UTF-8') ?>">Descargar QR</button>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <p class="status">Entorno detectado: <strong><?= htmlspecialchars($host, ENT_QUOTES, 'UTF-8') ?></strong></p>
    </main>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        document.querySelectorAll('.qr-box').forEach(function (container) {
            if (typeof QRCode === 'undefined') {
                container.innerHTML = '<p class="error">No fue posible cargar el código QR. Usa el botón Abrir formulario.</p>';
                return;
            }

            new QRCode(container, {
                text: container.dataset.url,
                width: 222,
                height: 222,
                colorDark: '#14213d',
                colorLight: '#ffffff',
                correctLevel: QRCode.CorrectLevel.H
            });
        });

        document.querySelectorAll('[data-download]').forEach(function (button) {
            button.addEventListener('click', function () {
                const container = document.getElementById(button.dataset.download);
                const canvas = container.querySelector('canvas');
                const image = container.querySelector('img');
                const href = canvas ? canvas.toDataURL('image/png') : (image ? image.src : '');
                if (!href) return;

                const link = document.createElement('a');
                link.href = href;
                link.download = 'qr-' + button.dataset.name + '.png';
                link.click();
            });
        });
    </script>
</body>
</html>
