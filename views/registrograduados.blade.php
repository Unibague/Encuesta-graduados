<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de graduados - Universidad de Ibagué</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1A3A6B;
            --primary-dark: #0F2747;
            --primary-light: #E8EEF7;
            --success: #16a672;
            --success-light: #e4f7f0;
            --danger: #ef4444;
            --danger-light: #fef2f2;
            --text: #1f2333;
            --text-muted: #7c8093;
            --border: #e6e5f1;
            --shadow: 0 20px 45px -20px rgba(26, 58, 107, 0.35);
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #f6f6fb;
            color: var(--text);
        }

        .encuesta-shell {
            min-height: 100vh;
            padding: 40px 16px 60px;
            background:
                radial-gradient(circle at 12% 8%, rgba(26, 58, 107, 0.18), transparent 40%),
                radial-gradient(circle at 88% 92%, rgba(22, 166, 114, 0.14), transparent 42%),
                #f6f6fb;
            display: flex;
            justify-content: center;
        }

        .encuesta-container {
            max-width: 680px;
            width: 100%;
        }

        .encuesta-intro {
            text-align: center;
            margin-bottom: 28px;
        }

        .encuesta-intro h1 {
            font-size: 1.9rem;
            font-weight: 800;
            margin: 0 0 8px;
            letter-spacing: -0.02em;
        }

        .encuesta-intro p {
            color: var(--text-muted);
            margin: 0;
            font-size: 0.95rem;
        }

        .progress-container { margin-bottom: 22px; }

        .progress-top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 10px;
            gap: 10px;
        }

        .section-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--primary-light);
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 12.5px;
            padding: 6px 12px;
            border-radius: 999px;
            white-space: nowrap;
        }

        .progress-meta {
            font-size: 12.5px;
            color: var(--text-muted);
            font-weight: 600;
            white-space: nowrap;
        }

        .progress-bar-bg {
            height: 8px;
            background: #e9e8f5;
            border-radius: 999px;
            overflow: hidden;
        }

        .progress-bar-fill {
            height: 100%;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--primary), #3B5B8C);
            transition: width 0.45s cubic-bezier(.4,0,.2,1);
        }

        .survey-card {
            background: #ffffff;
            border-radius: 26px;
            padding: 36px 32px 28px;
            box-shadow: var(--shadow);
            border: 1px solid rgba(26, 58, 107, 0.12);
            position: relative;
            overflow: hidden;
            min-height: 360px;
            display: flex;
            flex-direction: column;
        }

        .survey-card::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(26, 58, 107,0.12), transparent 70%);
            pointer-events: none;
        }

        @media (max-width: 560px) {
            .survey-card { padding: 28px 18px 22px; border-radius: 20px; }
        }

        .section-anim {
            animation: slideIn 0.32s cubic-bezier(.2,.8,.2,1);
        }

        .section-anim.leaving-back {
            animation: slideInBack 0.32s cubic-bezier(.2,.8,.2,1);
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(18px); }
            to { opacity: 1; transform: translateX(0); }
        }

        @keyframes slideInBack {
            from { opacity: 0; transform: translateX(-18px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .shake { animation: shake 0.4s; }

        @keyframes shake {
            10%, 90% { transform: translateX(-2px); }
            20%, 80% { transform: translateX(4px); }
            30%, 50%, 70% { transform: translateX(-7px); }
            40%, 60% { transform: translateX(7px); }
        }

        .section-title {
            font-size: 1.35rem;
            font-weight: 800;
            margin: 0 0 6px;
            letter-spacing: -0.01em;
        }

        .section-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin: 0 0 22px;
        }

        .form-field {
            margin-bottom: 22px;
            padding-bottom: 4px;
        }

        .form-field.has-error .input-field {
            border-color: var(--danger) !important;
            background: var(--danger-light) !important;
        }

        .form-field.has-error .chip-option {
            border-color: #fecaca;
        }

        .field-label {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 8px;
        }

        .field-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), #3B5B8C);
            color: #fff;
            font-weight: 800;
            font-size: 12px;
            flex-shrink: 0;
            box-shadow: 0 4px 10px -3px rgba(26, 58, 107,0.45);
        }

        .field-label-text {
            font-size: 0.98rem;
            font-weight: 700;
            line-height: 1.35;
            padding-top: 3px;
        }

        .field-label-text .required-mark {
            color: var(--primary-dark);
        }

        .field-desc {
            color: var(--text-muted);
            font-size: 0.85rem;
            margin: 0 0 10px 38px;
            line-height: 1.4;
        }

        .field-error {
            color: var(--danger);
            font-size: 12.5px;
            font-weight: 600;
            margin: 8px 0 0 38px;
            display: none;
        }

        .form-field.has-error .field-error {
            display: block;
        }

        .field-input-wrap {
            margin-left: 38px;
        }

        .input-field {
            width: 100%;
            padding: 13px 15px;
            border: 2px solid var(--border);
            border-radius: 14px;
            font-size: 0.98rem;
            font-family: inherit;
            color: var(--text);
            background: #fbfbfe;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
        }

        .input-field::placeholder { color: #b3b4c6; }

        .input-field:focus {
            outline: none;
            border-color: var(--primary);
            background: #ffffff;
            box-shadow: 0 0 0 4px rgba(26, 58, 107, 0.18);
        }

        textarea.input-field {
            min-height: 110px;
            resize: vertical;
            line-height: 1.5;
        }

        select.input-field {
            appearance: none;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='14' height='9' viewBox='0 0 14 9'><path d='M1 1l6 6 6-6' stroke='%237c8093' stroke-width='2' fill='none' fill-rule='evenodd' stroke-linecap='round' stroke-linejoin='round'/></svg>");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 42px;
            cursor: pointer;
        }

        .chip-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
        }

        .chip-option {
            position: relative;
            border: 2px solid var(--border);
            border-radius: 14px;
            padding: 13px 14px;
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text);
            cursor: pointer;
            background: #fbfbfe;
            transition: transform 0.15s ease, border-color 0.15s, background 0.15s, box-shadow 0.15s;
            user-select: none;
        }

        .chip-option:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px -8px rgba(31, 35, 51, 0.18);
        }

        .chip-option.selected {
            border-color: var(--primary);
            background: var(--primary-light);
            color: var(--primary-dark);
            padding-right: 32px;
        }

        .chip-option.selected::after {
            content: '✓';
            position: absolute;
            right: 11px;
            top: 50%;
            transform: translateY(-50%);
            font-weight: 800;
            color: var(--primary-dark);
        }

        .validation-alert {
            background: var(--danger-light);
            color: var(--danger);
            border: 1px solid #fecaca;
            border-radius: 12px;
            padding: 12px 14px;
            font-size: 13.5px;
            font-weight: 600;
            margin-bottom: 18px;
            display: none;
            align-items: center;
            gap: 8px;
        }

        .validation-alert.visible { display: flex; }

        .nav-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-top: auto;
            padding-top: 22px;
            border-top: 1px solid #f0f0f7;
        }

        .btn {
            border: none;
            border-radius: 13px;
            font-size: 14.5px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, transform 0.1s, box-shadow 0.2s;
            font-family: inherit;
        }

        .btn:active { transform: scale(0.97); }

        .btn-ghost {
            background: transparent;
            color: var(--text-muted);
            padding: 12px 6px;
        }

        .btn-ghost:hover { color: var(--text); }

        .btn-ghost:disabled {
            opacity: 0;
            pointer-events: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            padding: 13px 26px;
            box-shadow: 0 10px 20px -8px rgba(26, 58, 107, 0.55);
        }

        .btn-primary:hover { filter: brightness(1.05); }

        .btn-success {
            background: linear-gradient(135deg, var(--success), #12855e);
            color: #fff;
            padding: 13px 26px;
            box-shadow: 0 10px 20px -8px rgba(22, 166, 114, 0.5);
        }

        .btn-secondary-outline {
            background: #fff;
            color: var(--text);
            border: 2px solid var(--border);
            padding: 11px 22px;
        }

        .btn-secondary-outline:hover {
            border-color: var(--primary);
            color: var(--primary-dark);
        }

        .summary-head {
            text-align: center;
            margin-bottom: 22px;
        }

        .summary-check {
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: var(--success-light);
            color: var(--success);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 28px;
            margin: 0 auto 14px;
            animation: popIn 0.4s cubic-bezier(.2,.9,.3,1.3);
        }

        @keyframes popIn {
            from { transform: scale(0.5); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }

        .summary-head h2 {
            font-size: 1.35rem;
            font-weight: 800;
            margin: 0 0 4px;
        }

        .summary-head p {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin: 0;
        }

        .summary-group { margin-bottom: 18px; }

        .summary-group-title {
            font-size: 12px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: var(--primary-dark);
            margin: 0 0 8px;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .summary-item {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 10px;
            padding: 11px 4px;
            border-bottom: 1px solid #f0f0f7;
        }

        .summary-item:last-child { border-bottom: none; }

        .summary-texts { min-width: 0; }

        .summary-label {
            font-size: 12.5px;
            color: var(--text-muted);
            font-weight: 600;
        }

        .summary-value {
            color: var(--text);
            font-weight: 700;
            font-size: 0.94rem;
            margin-top: 2px;
            word-break: break-word;
        }

        .summary-edit {
            flex-shrink: 0;
            background: none;
            border: none;
            color: var(--primary-dark);
            font-weight: 700;
            font-size: 12.5px;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 8px;
        }

        .summary-edit:hover { background: var(--primary-light); }

        .thankyou-wrap {
            text-align: center;
            padding: 30px 0 10px;
        }

        .thankyou-icon {
            width: 76px;
            height: 76px;
            border-radius: 50%;
            background: var(--success-light);
            color: var(--success);
            font-size: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
            animation: popIn 0.5s cubic-bezier(.2,.9,.3,1.3);
        }

        .thankyou-wrap h2 {
            font-size: 1.5rem;
            font-weight: 800;
            margin: 0 0 8px;
        }

        .thankyou-wrap p {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin: 0 auto;
            max-width: 380px;
        }

        .consent-info {
            background: #f8f8fc;
            border-radius: 16px;
            padding: 20px 22px;
            margin-bottom: 24px;
            border: 1px solid var(--border);
            font-size: 0.92rem;
            line-height: 1.6;
            color: var(--text);
        }

        .consent-info p {
            margin: 0 0 14px;
        }

        .consent-info p:last-child {
            margin-bottom: 0;
        }

        .consent-policy {
            background: var(--primary-light);
            border-radius: 16px;
            padding: 20px 22px;
            margin-bottom: 22px;
            border: 1px solid rgba(26, 58, 107, 0.25);
            font-size: 0.88rem;
            line-height: 1.55;
            color: var(--text);
        }

        .consent-policy a {
            color: var(--primary-dark);
            font-weight: 700;
            text-decoration: underline;
        }

        .consent-rejected {
            text-align: center;
            padding: 40px 10px;
        }

        .consent-rejected-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: var(--danger-light);
            color: var(--danger);
            font-size: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
        }

        .consent-rejected h2 {
            font-size: 1.35rem;
            font-weight: 800;
            margin: 0 0 10px;
        }

        .consent-rejected p {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin: 0 auto;
            max-width: 360px;
        }
    </style>
</head>
<body>
    <div class="encuesta-shell">
        <div class="encuesta-container">

            <div class="encuesta-intro">
                <h1>Registro de graduados</h1>
                <p>Solo te tomará unos minutos. Completa cada sección a tu ritmo.</p>
            </div>

            <div class="progress-container">
                <div class="progress-top-row">
                    <span class="section-pill" id="sectionPill">📋 Cargando…</span>
                    <span class="progress-meta" id="progressMeta">Sección 1</span>
                </div>
                <div class="progress-bar-bg">
                    <div class="progress-bar-fill" id="progressBar" style="width: 0%"></div>
                </div>
            </div>

            <div class="survey-card">

                <div id="formView">
                    <div id="sectionArea" class="section-anim"></div>

                    <div class="nav-row">
                        <button type="button" class="btn btn-ghost" id="btnPrev" onclick="goBack()">← Atrás</button>
                        <button type="button" class="btn btn-primary" id="btnNext" onclick="goNext()">Continuar →</button>
                    </div>
                </div>

                <div id="summaryView" style="display: none;">
                    <div class="summary-head">
                        <div class="summary-check">📝</div>
                        <h2>Revisa tus respuestas</h2>
                        <p>Puedes editar cualquier campo antes de enviar la encuesta.</p>
                    </div>
                    <div id="summaryContainer"></div>
                    <div class="nav-row">
                        <button type="button" class="btn btn-secondary-outline" onclick="backToLastSection()">← Seguir editando</button>
                        <button type="button" class="btn btn-success" onclick="submitSurvey()">Terminar encuesta ✓</button>
                    </div>
                </div>

                <div id="thankyouView" style="display: none;">
                    <div class="thankyou-wrap">
                        <div class="thankyou-icon">✓</div>
                        <h2>¡Gracias por responder!</h2>
                        <p>Tus respuestas fueron registradas correctamente. Agradecemos el tiempo que dedicaste a esta encuesta.</p>
                    </div>
                </div>

                <div id="rejectedView" style="display: none;">
                    <div class="consent-rejected">
                        <div class="consent-rejected-icon">✕</div>
                        <h2>No es posible continuar</h2>
                        <p>Para participar en esta actualización de datos es necesario autorizar el tratamiento de la información según la política de la Universidad de Ibagué.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script type="module">
        import { Country, City } from 'https://cdn.jsdelivr.net/npm/country-state-city@3.2.1/+esm';

        window.Country = Country;
        window.City = City;

        const encuestaSections = [
            {
                title: 'Consentimiento',
                icon: '',
                isConsent: true,
                fields: [
                    {
                        key: 'autorizacion_datos',
                        label: '¿Autoriza el tratamiento de sus datos personales?',
                        type: 'radio',
                        options: ['Sí', 'No'],
                        required: true
                    }
                ]
            },
            {
                title: 'Datos personales',
                icon: '',
                fields: [
                    { key: 'nombres', label: 'Nombres', type: 'text', required: true, placeholder: 'Tus nombres completos' },
                    { key: 'apellidos', label: 'Apellidos', type: 'text', required: true, placeholder: 'Tus apellidos completos' },
                    { key: 'id', label: 'Cédula', type: 'text', description: 'Documento oficial', required: true, placeholder: 'Ej: 20231001' },
                    { key: 'email', label: 'Correo electrónico', type: 'email', description: 'Tu correo de contacto principal', required: true, placeholder: 'correo@ejemplo.com' },
                    { key: 'numero_celular', label: 'Número celular', type: 'tel', description: 'Teléfono de contacto', required: true, placeholder: 'Ej: +57 3001234567' },
                    { key: 'fecha_nacimiento', label: 'Fecha de nacimiento', type: 'date', required: true },
                    { key: 'genero', label: 'Género', type: 'radio', options: ['Masculino', 'Femenino', 'Transgénero', 'Otro'], required: true },
                    { key: 'direccion', label: 'Dirección', type: 'text', description: 'Dirección de residencia', required: true, placeholder: 'Calle 123 #45-67' },
                    { key: 'pais', label: 'País', type: 'country', required: true },
                    { key: 'ciudad', label: 'Ciudad', type: 'city', required: true }
                ]
            },
            {
                title: 'Formación académica',
                icon: '',
                fields: [
                    { key: 'nivel_academico', label: 'Máximo nivel académico alcanzado', type: 'select', options: ['Técnico', 'Tecnológico', 'Universitario', 'Especialización', 'Maestría', 'Doctorado'], required: true },
                    { key: 'anio_graduacion', label: 'Año de graduación', type: 'number', description: 'Año en que obtuviste tu título', required: true, placeholder: 'Ej: 2020' },
                    {
                        key: 'programa',
                        label: '¿De qué programa eres egresado?',
                        type: 'select',
                        description: 'Selecciona tu programa de la lista',
                        options: [
                            'TECNOLOGIA EN CONTABILIDAD Y COSTOS',
                            'TECNOLOGIA EN ENTRENAMIENTO DEPORTIVO EN FUTBOL',
                            'ESPECIALIZACION EN DERECHO ADMINISTRATIVO',
                            'ESPECIALIZACION EN GESTION EMPRESARIAL',
                            'MAESTRIA EN ADMINISTRACION DE NEGOCIOS',
                            'ESPECIALIZACION EN GESTION Y CONTROL DE CALIDAD',
                            'INGENIERIA DE SISTEMAS',
                            'ESPECIALIZACION EN INTERVENCION PSICOSOCIAL',
                            'CONTADURIA PUBLICA',
                            'TECNOLOGIA EN SEGURIDAD E HIGIENE INDUSTRIAL',
                            'TECNOLOGIA EN GESTION DE TIC',
                            'MAESTRIA EN DERECHO CON ENFASIS EN DERECHO PUBLICO Y DERECHO PRIVADO',
                            'TECNOLOGIA EN INVESTIGACION CRIMINAL Y JUDICIAL',
                            'INGENIERÍA EN ANALÍTICA DE DATOS',
                            'MAESTRIA EN GESTION TERRITORIAL, AUTONOMIA Y SOSTENIBILIDAD',
                            'TECNOLOGIA INDUSTRIAL',
                            'ESPECIALIZACION EN DERECHO PENAL',
                            'ARQUITECTURA',
                            'BIOLOGIA AMBIENTAL',
                            'INGENIERIA INDUSTRIAL',
                            'TECNOLOGIA MECANICA',
                            'INGENIERIA CIVIL',
                            'MAESTRIA EN ANALITICA DE DATOS PARA LA TOMA DE DECISIONES',
                            'MAESTRIA EN INGENIERIA DE CONTROL',
                            'COMUNICACION SOCIAL Y PERIODISMO',
                            'INGENIERIA MECANICA',
                            'MAESTRIA EN GERENCIA DE LA CALIDAD',
                            'PSICOLOGIA',
                            'ESP. EN GESTION DE OPERACIONES Y LOGISTA',
                            'TECNOLOGIA EN REDES Y COMUNICACIONES',
                            'TECNOLOGIA EN SISTEMAS',
                            'INGENIERIA ELECTRONICA',
                            'MAESTRIA EN GESTION INDUSTRIAL',
                            'TECNOLOGIA EN MANTENIMIENTO INDUSTRIAL',
                            'TECNOLOGIA EN MERCADEO Y VENTAS',
                            'TECNOLOGIA EN LOGISTICA',
                            'DISEÑO',
                            'DERECHO',
                            'ADMINISTRACION DE EMPRESAS',
                            'TECNOLOGIA EN ELECTRONICA',
                            'ADMINISTRACION DE NEGOCIOS INTERNACIONALES',
                            'ECONOMIA',
                            'ESPECIALIZACION EN DERECHO CIVIL',
                            'MERCADEO'
                        ],
                        required: true
                    }
                ]
            },
            {
                title: 'Situación laboral',
                icon: '',
                fields: [
                    { key: 'estado_laboral', label: '¿Cuál es tu situación laboral actual?', type: 'radio', options: ['Empleado', 'Desempleado', 'Independiente', 'Estudiante de postgrado'], required: true }
                ]
            },
            {
                title: 'Vinculación laboral',
                icon: '',
                fields: [
                    {
                        key: 'nombre_empresa',
                        label: 'Nombre de la empresa a la cual estás vinculado',
                        type: 'text',
                        required: true,
                        placeholder: 'Universidad de Ibagué',
                        showIf: (a) => a.estado_laboral === 'Empleado' || a.estado_laboral === 'Independiente'
                    },
                    {
                        key: 'sector',
                        label: 'Sector de la empresa',
                        type: 'radio',
                        options: ['Privado', 'Público', 'Mixto', 'No aplica'],
                        required: true,
                        showIf: (a) => a.estado_laboral === 'Empleado' || a.estado_laboral === 'Independiente'
                    },
                    {
                        key: 'sector_eco',
                        label: 'Sector económico de la empresa',
                        type: 'radio',
                        options: ['Industrial', 'Comercial', 'Servicios', 'Financiero', 'Agrario', 'Educación', 'Salud', 'Fuerzas Militares', 'ONG', 'No aplica'],
                        required: true,
                        showIf: (a) => a.estado_laboral === 'Empleado' || a.estado_laboral === 'Independiente'
                    }
                ]
            },
            {
                title: 'Tiempo cesante',
                icon: '',
                fields: [
                    {
                        key: 'tiempo_cesante',
                        label: '¿Cuánto tiempo llevas cesante?',
                        type: 'radio',
                        options: ['Menos de 1 año', 'De 1 a 5 años', 'De 6 a 10 años', 'Más de 10 años', 'No aplica'],
                        required: true,
                        showIf: (a) => a.estado_laboral === 'Desempleado'
                    }
                ]
            }
        ];

        const answers = {};
        let currentSectionIndex = 0;
        let returnToSummary = false;

        function getVisibleSections() {
            return encuestaSections
                .map(section => ({
                    ...section,
                    fields: section.fields.filter(f => !f.showIf || f.showIf(answers))
                }))
                .filter(section => section.fields.length > 0);
        }

        function isFieldEmpty(field) {
            const val = answers[field.key];
            if (field.type === 'checkbox') {
                return !Array.isArray(val) || val.length === 0;
            }
            return val === undefined || val === null || String(val).trim() === '';
        }

        function updateNextButton() {
            const sections = getVisibleSections();
            const total = sections.length;
            const btnNext = document.getElementById('btnNext');
            if (!btnNext) return;

            const isLast = currentSectionIndex >= total - 1;
            btnNext.textContent = isLast ? 'Ver resumen →' : 'Continuar →';
            btnNext.className = isLast ? 'btn btn-success' : 'btn btn-primary';

            document.getElementById('progressMeta').textContent = `Sección ${currentSectionIndex + 1} de ${total}`;
            const percent = Math.round((currentSectionIndex / Math.max(total, 1)) * 100);
            document.getElementById('progressBar').style.width = percent + '%';
        }

        function fieldHtml(field) {
            const value = answers[field.key] !== undefined ? answers[field.key] : '';
            const placeholder = field.placeholder || '';

            if (field.type === 'radio' || field.type === 'checkbox') {
                const selectedValues = field.type === 'checkbox'
                    ? (Array.isArray(value) ? value : [])
                    : [value];

                return `
                    <div class="chip-grid" data-key="${field.key}">
                        ${(field.options || []).map(opt => `
                            <div class="chip-option ${selectedValues.includes(opt) ? 'selected' : ''}"
                                 data-value="${opt.replace(/"/g, '&quot;')}"
                                 tabindex="0"
                                 onclick="selectChip('${field.key}', this.dataset.value, ${field.type === 'checkbox'})"
                                 onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault(); this.click();}">
                                ${opt}
                            </div>
                        `).join('')}
                    </div>
                `;
            }

            if (field.type === 'country') {
                const countries = Country.getAllCountries();

                return `
                    <select class="input-field" id="field_${field.key}" onchange="onCountryChange()">
                        <option value="">— Selecciona un país —</option>
                        ${countries.map(country => `
                            <option
                                value="${country.isoCode}"
                                data-name="${country.name.replace(/"/g, '&quot;')}"
                                ${answers.pais_codigo === country.isoCode ? 'selected' : ''}
                            >
                                ${country.name}
                            </option>
                        `).join('')}
                    </select>
                `;
            }

            if (field.type === 'city') {
                return `
                    <select
                        class="input-field"
                        id="field_${field.key}"
                        onchange="onInputChange('${field.key}')"
                        ${answers.pais_codigo ? '' : 'disabled'}
                    >
                        <option value="">
                            ${answers.pais_codigo
                                ? '— Selecciona una ciudad —'
                                : '— Primero selecciona un país —'}
                        </option>
                    </select>
                `;
            }

            if (field.type === 'select') {
                const options = (field.options || []).map(opt =>
                    `<option value="${opt.replace(/"/g, '&quot;')}" ${value === opt ? 'selected' : ''}>${opt}</option>`
                ).join('');

                return `
                    <select class="input-field" id="field_${field.key}" onchange="onInputChange('${field.key}')">
                        <option value="">— Selecciona una opción —</option>
                        ${options}
                    </select>
                `;
            }

            if (field.type === 'textarea') {
                return `<textarea class="input-field" id="field_${field.key}" placeholder="${placeholder}" oninput="onInputChange('${field.key}')">${value}</textarea>`;
            }

            return `<input class="input-field" type="${field.type}" id="field_${field.key}" value="${value}" placeholder="${placeholder}" autocomplete="off" oninput="onInputChange('${field.key}')">`;
        }

        function selectChip(key, value, isMulti) {
            if (isMulti) {
                const current = Array.isArray(answers[key]) ? answers[key] : [];
                answers[key] = current.includes(value)
                    ? current.filter(v => v !== value)
                    : [...current, value];
            } else {
                answers[key] = value;
            }

            const grid = document.querySelector(`.chip-grid[data-key="${key}"]`);
            if (grid) {
                const selected = isMulti
                    ? (Array.isArray(answers[key]) ? answers[key] : [])
                    : [answers[key]];

                grid.querySelectorAll('.chip-option').forEach(el => {
                    el.classList.toggle('selected', selected.includes(el.dataset.value));
                });
            }

            const fieldEl = document.querySelector(`.form-field[data-key="${key}"]`);
            if (fieldEl && !isFieldEmpty({ key, type: isMulti ? 'checkbox' : 'radio' })) {
                fieldEl.classList.remove('has-error');
            }

            updateNextButton();
        }

        function onInputChange(key) {
            const el = document.getElementById(`field_${key}`);
            if (el) {
                answers[key] = el.value.trim();
                const fieldEl = document.querySelector(`.form-field[data-key="${key}"]`);
                if (fieldEl && answers[key]) {
                    fieldEl.classList.remove('has-error');
                }
                updateNextButton();
            }
        }

        function onCountryChange() {
            const countrySelect = document.getElementById('field_pais');
            const citySelect = document.getElementById('field_ciudad');

            if (!countrySelect || !citySelect) return;

            const countryCode = countrySelect.value;
            const selectedOption = countrySelect.options[countrySelect.selectedIndex];
            const countryName = selectedOption?.dataset.name || '';

            answers.pais = countryName;
            answers.pais_codigo = countryCode;
            answers.ciudad = '';

            citySelect.innerHTML = '';

            if (!countryCode) {
                citySelect.disabled = true;
                citySelect.innerHTML = `<option value="">— Primero selecciona un país —</option>`;
                updateNextButton();
                return;
            }

            citySelect.disabled = false;
            citySelect.innerHTML = `<option value="">— Selecciona una ciudad —</option>`;

            const cities = City.getCitiesOfCountry(countryCode);

            if (!cities || cities.length === 0) {
                citySelect.innerHTML = `<option value="">— No hay ciudades disponibles —</option>`;
                updateNextButton();
                return;
            }

            cities
                .sort((a, b) => a.name.localeCompare(b.name))
                .forEach(city => {
                    const option = document.createElement('option');
                    option.value = city.name;
                    option.textContent = city.name;
                    citySelect.appendChild(option);
                });

            const countryField = document.querySelector('.form-field[data-key="pais"]');
            if (countryField) countryField.classList.remove('has-error');

            updateNextButton();
        }

        function saveCurrentSectionAnswers() {
            const sections = getVisibleSections();
            const section = sections[currentSectionIndex];
            if (!section) return;

            section.fields.forEach(field => {
                if (field.type === 'radio' || field.type === 'checkbox') return;

                if (field.type === 'country') {
                    const countrySelect = document.getElementById('field_pais');
                    if (countrySelect && countrySelect.value) {
                        const selectedOption = countrySelect.options[countrySelect.selectedIndex];
                        answers.pais_codigo = countrySelect.value;
                        answers.pais = selectedOption.dataset.name || '';
                    }
                    return;
                }

                const el = document.getElementById(`field_${field.key}`);
                if (el) answers[field.key] = el.value.trim();
            });
        }

        function validateCurrentSection() {
            const sections = getVisibleSections();
            const section = sections[currentSectionIndex];
            if (!section) return true;

            let isValid = true;
            let firstError = null;

            document.querySelectorAll('.form-field').forEach(el => el.classList.remove('has-error'));
            const alertEl = document.getElementById('validationAlert');
            if (alertEl) alertEl.classList.remove('visible');

            section.fields.forEach(field => {
                if (field.required && isFieldEmpty(field)) {
                    isValid = false;
                    const fieldEl = document.querySelector(`.form-field[data-key="${field.key}"]`);
                    if (fieldEl) {
                        fieldEl.classList.add('has-error');
                        if (!firstError) firstError = fieldEl;
                    }
                }
            });

            if (!isValid) {
                if (alertEl) alertEl.classList.add('visible');
                if (firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                const card = document.querySelector('.survey-card');
                card.classList.add('shake');
                setTimeout(() => card.classList.remove('shake'), 400);
            }

            return isValid;
        }

        function renderSection(direction) {
            const sections = getVisibleSections();

            if (currentSectionIndex >= sections.length) {
                showSummary();
                return;
            }
            if (currentSectionIndex < 0) currentSectionIndex = 0;

            const section = sections[currentSectionIndex];
            const total = sections.length;
            const percent = Math.round((currentSectionIndex / total) * 100);

            document.getElementById('sectionPill').textContent = `${section.icon} ${section.title}`;
            document.getElementById('progressMeta').textContent = `Sección ${currentSectionIndex + 1} de ${total}`;
            document.getElementById('progressBar').style.width = percent + '%';

            let globalOffset = 0;
            for (let i = 0; i < currentSectionIndex; i++) {
                globalOffset += sections[i].fields.length;
            }

            let consentHtml = '';
            if (section.isConsent) {
                consentHtml = `
                    <div class="consent-info">
                        <p>Los graduados son actores sociales que representan los valores de la Universidad de Ibagué y dan sentido al existir de la Institución. Son agentes de cambio y embajadores Unibagué en su ejercicio laboral y social. Por ello, lo invitamos a actualizar sus datos para mantenernos en contacto.</p>
                        <p>La actualización nos permitirá informarle sobre las actividades, talleres, beneficios, eventos, ofertas comerciales y programas que la Universidad tiene para usted.</p>
                        <p>Tenga en cuenta que su participación es fundamental. Todas sus respuestas son confidenciales, según lo contemplado en la Ley 1581 de 2012 y tendrán un trato especial, se mantienen bajo estrictas medidas de seguridad y solo el personal autorizado tendrá acceso a ellas. Los datos obtenidos serán utilizados y procesados estadísticamente para los propósitos anteriormente señalados y para comunicaciones relacionadas con ofertas institucionales.</p>
                    </div>
                    <div class="consent-policy">
                        <strong>Política de tratamiento de datos.</strong> Autorizo expresamente a la Universidad de Ibagué, a quien le hago entrega de mis datos personales de forma libre y voluntaria, previa, explícita, informada e inequívoca, para que puedan ser utilizados de conformidad con las finalidades establecidas en la política de tratamiento de datos la cual podrá ser consultada a través de la página web: <a href="https://www.unibague.edu.co/" target="_blank" rel="noopener">https://www.unibague.edu.co/</a>. Declaro que conozco que en cualquier momento podré solicitar a la Universidad de Ibagué, la actualización, rectificación y supresión de los datos suministrados, dirigiéndome al correo electrónico: <a href="mailto:habeasdata@unibague.edu.co">habeasdata@unibague.edu.co</a>. Acepto las condiciones dispuestas en la presente consulta. Al diligenciar este formato autorizo el uso de los datos aquí consignados. Adicional autorizo el tratamiento de mis datos personales para recibir información sobre la oferta académica, programas de posgrado, educación continua, extensión, eventos y demás servicios académicos o formativos de la Universidad, así como para que se realicen actividades de seguimiento comercial, mercadeo, promoción y orientación a través de llamadas telefónicas, correo electrónico, WhatsApp, mensajes de texto u otros medios físicos o digitales.
                    </div>
                `;
            }

            const fieldsHtml = section.fields.map((field, idx) => {
                const num = globalOffset + idx + 1;
                const desc = field.description
                    ? `<p class="field-desc">${field.description}</p>`
                    : '';
                const optionalTag = field.required
                    ? ''
                    : ' <span style="font-size:11px;font-weight:700;color:var(--text-muted);background:#f1f1f8;padding:2px 8px;border-radius:999px;margin-left:6px;">Opcional</span>';

                return `
                    <div class="form-field" data-key="${field.key}">
                        <div class="field-label">
                            <span class="field-number">${num}</span>
                            <span class="field-label-text">${field.label}${field.required ? ' <span class="required-mark">*</span>' : ''}${optionalTag}</span>
                        </div>
                        ${desc}
                        <div class="field-input-wrap">
                            ${fieldHtml(field)}
                        </div>
                        <div class="field-error">Este campo es obligatorio</div>
                    </div>
                `;
            }).join('');

            const area = document.getElementById('sectionArea');
            area.innerHTML = `
                <div class="validation-alert" id="validationAlert">
                    ⚠️ Completa todos los campos obligatorios antes de continuar.
                </div>
                <h2 class="section-title">${section.icon} ${section.title}</h2>
                <p class="section-subtitle">${section.isConsent ? 'Lee la información y acepta para continuar' : section.fields.length + ' pregunta' + (section.fields.length !== 1 ? 's' : '') + ' en esta sección'}</p>
                ${consentHtml}
                ${fieldsHtml}
            `;

            area.classList.remove('section-anim', 'leaving-back');
            void area.offsetWidth;
            area.classList.add(direction === 'back' ? 'leaving-back' : 'section-anim');

            const btnPrev = document.getElementById('btnPrev');
            btnPrev.disabled = currentSectionIndex === 0;
            updateNextButton();

            document.getElementById('formView').style.display = 'block';
            document.getElementById('summaryView').style.display = 'none';
            document.getElementById('thankyouView').style.display = 'none';
            document.getElementById('rejectedView').style.display = 'none';

            if (answers.pais_codigo) {
                const countrySelect = document.getElementById('field_pais');
                if (countrySelect) {
                    countrySelect.value = answers.pais_codigo;

                    const citySelect = document.getElementById('field_ciudad');
                    if (citySelect) {
                        const cities = City.getCitiesOfCountry(answers.pais_codigo);
                        citySelect.disabled = false;
                        citySelect.innerHTML = `<option value="">— Selecciona una ciudad —</option>`;

                        cities
                            .sort((a, b) => a.name.localeCompare(b.name))
                            .forEach(city => {
                                const option = document.createElement('option');
                                option.value = city.name;
                                option.textContent = city.name;
                                if (city.name === answers.ciudad) option.selected = true;
                                citySelect.appendChild(option);
                            });
                    }
                }
            }
        }

        function showRejected() {
            document.getElementById('formView').style.display = 'none';
            document.getElementById('summaryView').style.display = 'none';
            document.getElementById('thankyouView').style.display = 'none';
            document.getElementById('rejectedView').style.display = 'block';
            document.getElementById('sectionPill').textContent = '✕ Finalizado';
            document.getElementById('progressMeta').textContent = 'No autorizado';
            document.getElementById('progressBar').style.width = '100%';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function goNext() {
            if (!validateCurrentSection()) return;
            saveCurrentSectionAnswers();

            const sections = getVisibleSections();
            const current = sections[currentSectionIndex];
            if (current && current.isConsent && answers.autorizacion_datos === 'No') {
                showRejected();
                return;
            }

            if (returnToSummary) {
                returnToSummary = false;
                showSummary();
                return;
            }

            if (currentSectionIndex < sections.length - 1) {
                currentSectionIndex++;
                renderSection('next');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                showSummary();
            }
        }

        function goBack() {
            if (currentSectionIndex === 0) return;
            saveCurrentSectionAnswers();
            returnToSummary = false;
            currentSectionIndex--;

            const sections = getVisibleSections();
            if (currentSectionIndex >= sections.length) {
                currentSectionIndex = Math.max(0, sections.length - 1);
            }
            renderSection('back');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        function editField(key) {
            const sections = getVisibleSections();
            let targetSection = -1;
            for (let i = 0; i < sections.length; i++) {
                if (sections[i].fields.some(f => f.key === key)) {
                    targetSection = i;
                    break;
                }
            }
            if (targetSection === -1) return;

            currentSectionIndex = targetSection;
            returnToSummary = true;
            document.getElementById('summaryView').style.display = 'none';
            document.getElementById('formView').style.display = 'block';
            renderSection('back');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            setTimeout(() => {
                const el = document.querySelector(`.form-field[data-key="${key}"]`);
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        }

        function backToLastSection() {
            const sections = getVisibleSections();
            currentSectionIndex = Math.max(0, sections.length - 1);
            returnToSummary = false;
            document.getElementById('summaryView').style.display = 'none';
            document.getElementById('formView').style.display = 'block';
            renderSection('back');
        }

        function showSummary() {
            saveCurrentSectionAnswers();
            const sections = getVisibleSections();
            const container = document.getElementById('summaryContainer');

            const sectionsToShow = sections.filter(s => !s.isConsent);

            container.innerHTML = sectionsToShow.map(section => `
                <div class="summary-group">
                    <p class="summary-group-title">${section.icon} ${section.title}</p>
                    ${section.fields.map(field => {
                        let val = answers[field.key];
                        if (Array.isArray(val)) {
                            val = val.length ? val.join(', ') : '—';
                        } else if (!val && val !== 0) {
                            val = '—';
                        }
                        return `
                            <div class="summary-item">
                                <div class="summary-texts">
                                    <div class="summary-label">${field.label}</div>
                                    <div class="summary-value">${val}</div>
                                </div>
                                <button type="button" class="summary-edit" onclick="editField('${field.key}')">Editar</button>
                            </div>
                        `;
                    }).join('')}
                </div>
            `).join('');

            document.getElementById('formView').style.display = 'none';
            document.getElementById('summaryView').style.display = 'block';
            document.getElementById('thankyouView').style.display = 'none';
            document.getElementById('rejectedView').style.display = 'none';
            document.getElementById('sectionPill').textContent = '📝 Resumen';
            document.getElementById('progressMeta').textContent = 'Completado';
            document.getElementById('progressBar').style.width = '100%';
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        async function submitSurvey() {
            saveCurrentSectionAnswers();
            const button = document.querySelector('#summaryView .btn-success');
            if (button) button.disabled = true;

            try {
                const response = await fetch('/api/save-survey.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        survey_type: 'registrograduados',
                        answers,
                    }),
                });
                const result = await response.json();
                if (!response.ok || !result.success) {
                    throw new Error(result.message || 'No se pudo guardar la encuesta');
                }

                document.getElementById('summaryView').style.display = 'none';
                document.getElementById('thankyouView').style.display = 'block';
                document.getElementById('progressMeta').textContent = 'Finalizado';
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } catch (error) {
                if (button) button.disabled = false;
                alert(error.message);
            }
        }

        window.selectChip = selectChip;
        window.onInputChange = onInputChange;
        window.onCountryChange = onCountryChange;
        window.goNext = goNext;
        window.goBack = goBack;
        window.editField = editField;
        window.backToLastSection = backToLastSection;
        window.submitSurvey = submitSurvey;

        renderSection();
    </script>
</body>
</html>