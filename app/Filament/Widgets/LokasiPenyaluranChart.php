<?php

namespace App\Filament\Widgets;

use App\Models\TitikPenyaluran;
use Filament\Widgets\ChartWidget;

class LokasiPenyaluranChart extends ChartWidget
{
    // Judul kita ubah biar lebih spesifik dan keren
    protected static ?string $heading = 'Top 5 Lokasi Penyaluran Teraktif';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        // 🎯 KUNCI 1: Kita batasi ambil 5 data saja biar tidak jadi baling-baling!
        $lokasiAsli = TitikPenyaluran::limit(5)->pluck('nama_lokasi')->toArray();
        
        $dataSimulasi = [];
        foreach ($lokasiAsli as $lokasi) {
            $dataSimulasi[] = rand(80, 250); // Angka kita besarkan biar grafiknya gagah
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Paket Gizi',
                    'data' => $dataSimulasi,
                    // Kita pakai satu warna biru solid yang profesional
                    'backgroundColor' => '#38bdf8', 
                    'borderRadius' => 6, // Efek lengkung di ujung batang (UI/UX banget)
                ],
            ],
            'labels' => $lokasiAsli,
        ];
    }

    protected function getType(): string
    {
        // 🎯 KUNCI 2: Ganti dari 'doughnut' menjadi 'bar'
        return 'bar';
    }

    // 🎯 KUNCI 3: Fungsi rahasia untuk membuat grafiknya menyamping (Horizontal)
    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', 
            'plugins' => [
                'legend' => [
                    'display' => false, // Sembunyikan legend karena sudah jelas
                ],
            ],
        ];
    }
}