@component('templates.main')

    @slot('title')
        Graficos de datos actualizados
    @endslot

    @slot('header')
        <style>
            .page-scroll { overflow-x: auto; width: 100%; }
            .chart-card {
                background: #fff;
                border-radius: .5rem;
                box-shadow: 0 1px 4px rgba(0,0,0,.08);
                padding: 1.25rem;
                height: 100%;
            }
            .chart-wrap { position: relative; height: 320px; }
            .chart-total { font-size: .9rem; color: #6c757d; }
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

    <div class="page-scroll">

        <h1 class="text-center mb-1">Graficos</h1>
        <p class="text-center text-muted mb-4">{{ number_format($total) }} registro(s) en total.</p>

        @php
            $graduadosActualizados     = $graduadosActualizados     ?? 0;
            $graduadosNoActualizados   = $graduadosNoActualizados   ?? 0;
            $noGraduadosActualizados   = $noGraduadosActualizados   ?? 0;
            $noGraduadosNoActualizados = $noGraduadosNoActualizados ?? 0;

            $totalGraduados   = $graduadosActualizados + $graduadosNoActualizados;
            $totalNoGraduados = $noGraduadosActualizados + $noGraduadosNoActualizados;
        @endphp

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