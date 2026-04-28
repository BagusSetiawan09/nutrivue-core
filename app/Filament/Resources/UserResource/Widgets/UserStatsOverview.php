<?php

namespace App\Filament\Resources\UserResource\Widgets;

use App\Filament\Resources\UserResource;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UserStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Mengambil query dasar yang sudah terisolasi (Multi-Tenancy)
        $query = UserResource::getEloquentQuery();

        return [
            Stat::make('Total IT MBG', (clone $query)->where('role', 'it_mbg')->count())
                ->description('Pengelola Operasional Mitra')
                ->descriptionIcon('heroicon-m-computer-desktop')
                ->color('fuchsia'),
                
            Stat::make('Total Petugas', (clone $query)->where('role', 'petugas')->count())
                ->description('Petugas Lapangan Distribusi')
                ->descriptionIcon('heroicon-m-truck')
                ->color('warning'),
                
            Stat::make('Total Masyarakat', (clone $query)->where('role', 'masyarakat')->count())
                ->description('Penerima Manfaat Gizi')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }
}