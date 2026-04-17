@php
    // Inisialisasi data makronutrisi dari rekaman menu
    $record = $getRecord();
    $protein = $record->protein ?? 0;
    $karbo = $record->karbohidrat ?? 0;
    $lemak = $record->lemak ?? 0;
    
    // Validasi keberadaan data untuk menentukan visibilitas grafik
    $hasData = $protein > 0 || $karbo > 0 || $lemak > 0;
@endphp

<div style="width: 100%; height: 100%; display: flex; justify-content: center; align-items: center; padding: 1rem;">
    @if($hasData)
    <div
        x-data="{
            chart: null,
            // Inisialisasi pustaka grafik saat komponen dimuat
            init() {
                if (typeof Chart === 'undefined') {
                    const script = document.createElement('script');
                    script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
                    script.onload = () => this.drawChart();
                    document.head.appendChild(script);
                } else {
                    this.drawChart();
                }
            },
            // Logika penggambaran grafik komposisi gizi
            drawChart() {
                const ctx = this.$refs.canvas.getContext('2d');
                this.chart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Protein (g)', 'Karbohidrat (g)', 'Lemak (g)'],
                        datasets: [{
                            data: [{{ $protein }}, {{ $karbo }}, {{ $lemak }}],
                            backgroundColor: ['#ef4444', '#3b82f6', '#f59e0b'],
                            borderWidth: 2,
                            borderColor: '#18181b',
                            hoverOffset: 4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: { 
                                    color: '#d1d5db',
                                    usePointStyle: true,
                                    pointStyle: 'circle'
                                } 
                            }
                        }
                    }
                });
            }
        }"
        style="width: 100%; max-width: 350px; height: 350px; position: relative;"
    >
        <canvas x-ref="canvas"></canvas>
    </div>
    @else
        <p style="color: #9ca3af; font-style: italic;">Informasi komposisi gizi belum tersedia untuk visualisasi</p>
    @endif
</div>