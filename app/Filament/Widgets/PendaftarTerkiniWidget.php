<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendaftarTerkiniWidget extends BaseWidget
{
    protected static ?string $heading = 'Pendaftar / Pengguna Terkini';
    protected static ?int $sort = 4;
    protected int | string | array $columnSpan = 'full';

    public static function canView(): bool
    {
        return auth()->user()->role !== 'pemerintah';
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Ambil 5 user terbaru
                User::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('email')
                    ->label('Email')
                    ->icon('heroicon-m-envelope'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Waktu Daftar')
                    ->dateTime('d M Y, H:i')
                    ->badge()
                    ->color('success'),
            ])
            ->paginated(false);
    }
}