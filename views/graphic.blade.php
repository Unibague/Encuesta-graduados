@component('templates.main')

    @slot('title')
        Graficos de datos actualizados
    @endslot

    @slot('header')
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

            .graphic-page {
                min-height: 100vh;
                padding: 40px 16px 60px;
                background:
                    radial-gradient(circle at 12% 8%, rgba(26, 58, 107, 0.18), transparent 40%),
                    radial-gradient(circle at 88% 92%, rgba(22, 166, 114, 0.14), transparent 42%),
                    #f6f6fb;
                font-family: 'Manrope', -apple-system, BlinkMacSystemFont, sans-serif;
                color: var(--text);
            }

            .graphic-container {
                max-width: 1100px;
                width: 100%;
                margin: 0 auto;
            }

            .graphic-intro {
                text-align: center;
                margin-bottom: 28px;
            }

            .graphic-intro h1 {
                font-size: 1.9rem;
                font-weight: 800;
                letter-spacing: -0.02em;
                margin: 0 0 8px;
            }

            .graphic-intro p {
                color: var(--text-muted);
                font-size: .95rem;
                margin: 0;
            }

            .charts-card {
                background: #fff;
                border-radius: 26px;
                padding: 32px;
                box-shadow: var(--shadow);
                border: 1px solid rgba(26, 58, 107, 0.12);
            }

            .chart-card {
                background: #fff;
                border-radius: 20px;
                box-shadow: 0 10px 30px -18px rgba(31,35,51,.3);
                border: 1px solid var(--border);
                padding: 24px;
                height: 100%;
                transition: transform .2s ease, box-shadow .2s ease;
            }

            .chart-card:hover {
                transform: translateY(-2px);
                box-shadow: 0 16px 34px -18px rgba(31,35,51,.35);
            }

            .chart-card h5 {
                color: var(--text);
                font-weight: 800;
                margin: 0;
            }

            .chart-wrap {
                position: relative;
                height: 320px;
            }

            .chart-total {
                font-size: .9rem;
                color: var(--text-muted);
            }

            .toast.text-bg-success {
                background: var(--success) !important;
                border-radius: 14px !important;
                font-family: 'Manrope', sans-serif;
                font-weight: 600;
            }

            @media (max-width: 700px) {
                .graphic-page {
                    padding: 28px 12px 40px;
                }

                .charts-card {
                    padding: 20px 14px;
                    border-radius: 20px;
                }

                .graphic-intro h1 {
                    font-size: 1.6rem;
                }

                .chart-card {
                    padding: 18px;
                }

                .chart-wrap {
                    height: 280px;
                }
            }
        </style>
    @endslot

    @if(!empty($message))
        <div class="toast align-items-center text-bg-success border-0 position-fixed top-0 end-0 m-3"
             role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">{{ $message }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto"
                        data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif

    <div class="graphic-page">
        <div class="graphic-container">

        <div class="graphic-intro">
            <h1>Graficos</h1>
            <p>{{ number_format($total) }} registro(s) en total.</p>
        </div>

        @php
            $graduadosActualizados     = $graduadosActualizados     ?? 0;
            $graduadosNoActualizados   = $graduadosNoActualizados   ?? 0;
            $noGraduadosActualizados   = $noGraduadosActualizados   ?? 0;
            $noGraduadosNoActualizados = $noGraduadosNoActualizados ?? 0;

            $totalGraduados   = $graduadosActualizados + $graduadosNoActualizados;
            $totalNoGraduados = $noGraduadosActualizados + $noGraduadosNoActualizados;
        @endphp

        <div class="charts-card">
            <div class="row justify-content-center g-4">

            <div class="col-md-5">
                <div class="chart-card">
                    <h5 class="text-center mb-1">Graduados</h5>
                    <p class="text-center chart-total mb-3">{{ number_format($totalGraduados) }} registro(s)</p>
                    <div class="chart-wrap">
                        <canvas id="chartGraduados"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-5">
                <div class="chart-card">
                    <h5 class="text-center mb-1">No graduados</h5>
                    <p class="text-center chart-total mb-3">{{ number_format($totalNoGraduados) }} registro(s)</p>
                    <div class="chart-wrap">
                        <canvas id="chartNoGraduados"></canvas>
                    </div>
                </div>
            </div>

            </div>
        </div>
    </div>

    @slot('scripts')
    <script src="tools\chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.toast').forEach(el => {
                new bootstrap.Toast(el, { delay: 5000 }).show();
            });

            if (typeof Chart === 'undefined') {
                console.error('Chart.js no se cargó. Verifica la ruta del <script src="..."> en graphic.blade.php.');
                document.querySelectorAll('.chart-wrap').forEach(function (el) {
                    el.innerHTML = '<p class="text-center text-danger small mb-0">'
                        + 'No se pudo cargar la librería de gráficas (Chart.js). '
                        + 'Verifica que el archivo esté disponible en la ruta configurada.'
                        + '</p>';
                });
                return;
            }

            const commonOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                const value = ctx.parsed;
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const pct = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return `${ctx.label}: ${value} (${pct}%)`;
                            }
                        }
                    }
                }
            };

            new Chart(document.getElementById('chartGraduados'), {
                type: 'pie',
                data: {
                    labels: ['Actualizados', 'No actualizados'],
                    datasets: [{
                        data: [
                            {{ (int) $graduadosActualizados }},
                            {{ (int) $graduadosNoActualizados }}
                        ],
                        backgroundColor: ['#198754', '#dc3545']
                    }]
                },
                options: commonOptions
            });

            new Chart(document.getElementById('chartNoGraduados'), {
                type: 'pie',
                data: {
                    labels: ['Actualizados', 'No actualizados'],
                    datasets: [{
                        data: [
                            {{ (int) $noGraduadosActualizados }},
                            {{ (int) $noGraduadosNoActualizados }}
                        ],
                        backgroundColor: ['#0d6efd', '#dc3545']
                    }]
                },
                options: commonOptions
            });
        });
    </script>
    @endslot

@endcomponent