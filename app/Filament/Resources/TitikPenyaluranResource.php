<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TitikPenyaluranResource\Pages;
use App\Models\TitikPenyaluran;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Grid as InfolistGrid;

class TitikPenyaluranResource extends Resource
{
    protected static ?string $model = TitikPenyaluran::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Titik Penyaluran';
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Master Lokasi')->schema([
                    Forms\Components\TextInput::make('nama_lokasi')
                        ->required()
                        ->placeholder('Contoh: SDN 101877 Helvetia'),
                    Forms\Components\Select::make('jenis_lokasi')
                        ->options([
                            'Sekolah' => 'Sekolah',
                            'Posyandu' => 'Posyandu',
                            'Puskesmas' => 'Puskesmas',
                        ])->required(),
                    Forms\Components\TextInput::make('penanggung_jawab')
                        ->label('Nama Kepala/Pimpinan')
                        ->required(),
                    Forms\Components\TextInput::make('kontak_person')
                        ->tel()
                        ->label('No. WhatsApp Aktif'),
                    Forms\Components\Textarea::make('alamat')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('map_url')
                        ->url()
                        ->label('Link Google Maps')
                        ->columnSpanFull(),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lokasi')->searchable()->weight('bold'),
                Tables\Columns\TextColumn::make('jenis_lokasi')->badge()->color('info'),
                Tables\Columns\TextColumn::make('penanggung_jawab')->label('Pimpinan'),
                Tables\Columns\TextColumn::make('alamat')->limit(30),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ])->button()->label('Aksi')->color('gray'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Informasi Pimpinan & Lokasi')
                    ->schema([
                        TextEntry::make('nama_lokasi')
                            ->hiddenLabel()
                            ->weight('bold')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'text-2xl mb-2']),

                        InfolistGrid::make(3)->schema([
                            TextEntry::make('jenis_lokasi')
                                ->label('Kategori')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'Sekolah' => 'success',
                                    'Puskesmas' => 'danger',
                                    'Posyandu' => 'warning',
                                    default => 'gray',
                                })
                                ->icon(fn (string $state): string => match ($state) {
                                    'Sekolah' => 'heroicon-m-academic-cap',
                                    'Puskesmas' => 'heroicon-m-building-office-2',
                                    'Posyandu' => 'heroicon-m-home-modern',
                                    default => 'heroicon-m-map-pin',
                                }),

                            TextEntry::make('penanggung_jawab')
                                ->label('Pimpinan / Penanggung Jawab')
                                ->icon('heroicon-m-user-circle')
                                ->weight('bold'),

                            TextEntry::make('kontak_person')
                                ->label('Kontak (WhatsApp)')
                                ->icon('heroicon-m-phone')
                                ->color('success')
                                ->copyable()
                                ->copyMessage('Nomor berhasil disalin!'),
                        ]),
                    ]),

                InfoSection::make('Pemetaan & Alamat')
                    ->schema([
                        TextEntry::make('alamat')
                            ->icon('heroicon-m-map-pin')
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'italic text-gray-500']),

                        TextEntry::make('map_url')
                            ->label('Google Maps')
                            ->icon('heroicon-m-map')
                            ->formatStateUsing(fn () => 'Buka Lokasi di Google Maps')
                            ->url(fn ($record) => $record->map_url)
                            ->openUrlInNewTab()
                            ->badge()
                            ->color('info')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTitikPenyalurans::route('/'),
            'create' => Pages\CreateTitikPenyaluran::route('/create'),
            'edit' => Pages\EditTitikPenyaluran::route('/{record}/edit'),
        ];
    }
}