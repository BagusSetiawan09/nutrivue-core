<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\TitikPenyaluran;
use App\Models\Menu;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    // Mengatur urutan agar widget ini selalu tampil paling atas di Dashboard
    protected static ?int $sort = 1;

    // Mengatur waktu refresh otomatis setiap 10 detik (Real-time feel)
    protected static ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        return [
            // Metrik 1: Total Pengguna
            Stat::make('Total Penerima Aktif', User::count() ?? 0)
                ->description('Total peserta terdaftar di sistem')
                ->descriptionIcon('heroicon-m-users')
                ->chart([7, 2, 10, 3, 15, 4, 17]) // Efek grafik percikan (sparkline) palsu agar keren
                ->color('success'),

            // Metrik 2: Titik Penyaluran
            Stat::make('Titik Penyaluran Beroperasi', TitikPenyaluran::count() ?? 0)
                ->description('Lokasi distribusi gizi siap')
                ->descriptionIcon('heroicon-m-map-pin')
                ->color('info'),

            // Metrik 3: Total Menu
            Stat::make('Varian Menu Gizi', Menu::count() ?? 0)
                ->description('Tersedia untuk didistribusikan')
                ->descriptionIcon('heroicon-m-beaker')
                ->chart([3, 5, 4, 6, 8, 5, 10]) // Efek grafik percikan (sparkline) palsu
                ->color('warning'),
        ];
    }
}