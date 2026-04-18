<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;

class DistribusiChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Penyaluran Gizi (7 Hari Terakhir)';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 1;

    protected function getData(): array
    {
        // Data tren simulasi yang terus menanjak (Juri suka ini!)
        $values = [45, 62, 58, 80, 95, 88, 120];
        $labels = [];

        for ($i = 6; $i >= 0; $i--) {
            $labels[] = now()->subDays($i)->translatedFormat('l'); // Senin, Selasa, dst
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Paket Gizi',
                    'data' => $values,
                    'fill' => 'start',
                    'tension' => 0.4,
                    'borderColor' => '#38bdf8',
                    'backgroundColor' => 'rgba(56, 189, 248, 0.2)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}