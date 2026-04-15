<?php

namespace App\Filament\Resources\MenuResource\Widgets;

use App\Models\Menu;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MenuStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Menu Terjadwal', Menu::count())
                ->description('Seluruh jadwal MBG terdaftar')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color('primary'),

            Stat::make('Proses Pengantaran', Menu::where('status', 'Sedang Dikirim')->count())
                ->description('Sedang menuju lokasi distribusi')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning')
                ->chart([7, 3, 4, 5, 6, 3, 5, 3]),

            Stat::make('Selesai Distribusi', Menu::where('status', 'Selesai')->count())
                ->description('Telah diterima target sasaran')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success')
                ->chart([1, 3, 4, 7, 8, 9, 10, 12]),
        ];
    }
}