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

/**
 * Pengaturan Resource Titik Penyaluran
 * Mengelola data lokasi master distribusi seperti sekolah puskesmas atau posyandu
 */
class TitikPenyaluranResource extends Resource
{
    // Model referensi data titik penyaluran
    protected static ?string $model = TitikPenyaluran::class;

    // Konfigurasi ikon navigasi pada bilah sisi
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    // Label navigasi yang tampil pada menu sidebar
    protected static ?string $navigationLabel = 'Titik Penyaluran';

    public static function getNavigationGroup(): ?string
    {
        return auth()->user()->role === 'pemerintah' 
            ? 'Laporan Eksekutif' 
            : 'Master Data';
    }

    /**
     * Definisi skema formulir pengelolaan lokasi penyaluran
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Master Lokasi')->schema([
                    Forms\Components\TextInput::make('nama_lokasi')
                        ->label('Nama Lokasi')
                        ->required()
                        ->placeholder('Masukkan Nama Sekolah'),
                    
                    Forms\Components\Select::make('jenis_lokasi')
                        ->label('Kategori Lokasi')
                        ->options([
                            'Sekolah' => 'Sekolah',
                            'Posyandu' => 'Posyandu',
                            'Puskesmas' => 'Puskesmas',
                        ])->required(),

                    // PENAMBAHAN KOTAK ISIAN KODE RAHASIA
                    Forms\Components\TextInput::make('kode_rahasia')
                        ->label('Kode Rahasia Pendaftaran')
                        ->helperText('Wajib diisi untuk pendaftaran Siswa di aplikasi seluler')
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    
                    Forms\Components\TextInput::make('penanggung_jawab')
                        ->label('Nama Kepala Pimpinan')
                        ->required(),
                    
                    Forms\Components\TextInput::make('kontak_person')
                        ->label('No WhatsApp Aktif')
                        ->tel(),
                    
                    Forms\Components\Textarea::make('alamat')
                        ->label('Alamat Lengkap')
                        ->required()
                        ->columnSpanFull(),
                    
                    Forms\Components\TextInput::make('map_url')
                        ->label('Tautan Google Maps')
                        ->url()
                        ->columnSpanFull(),
                ])->columns(2)
            ]);
    }

    /**
     * Definisi struktur tabel daftar lokasi penyaluran
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lokasi')
                    ->label('Nama Lokasi')
                    ->searchable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('jenis_lokasi')
                    ->label('Jenis')
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('penanggung_jawab')
                    ->label('Nama Pimpinan'),
                
                Tables\Columns\TextColumn::make('alamat')
                    ->label('Alamat')
                    ->limit(30),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('Lihat Detail'),
                    Tables\Actions\EditAction::make()->label('Ubah Data'),
                    Tables\Actions\DeleteAction::make()->label('Hapus'),
                ])->button()->label('Aksi')->color('gray'),
            ]);
    }

    /**
     * Tampilan detail pemetaan dan informasi pimpinan lokasi
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Informasi Pimpinan Lokasi')
                    ->schema([
                        TextEntry::make('nama_lokasi')
                            ->hiddenLabel()
                            ->weight('bold')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'text-2xl mb-2']),

                        InfolistGrid::make(3)->schema([
                            TextEntry::make('jenis_lokasi')
                                ->label('Kategori Instansi')
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
                                ->label('Pimpinan Penanggung Jawab')
                                ->icon('heroicon-m-user-circle')
                                ->weight('bold'),

                            TextEntry::make('kontak_person')
                                ->label('Kontak WhatsApp Aktif')
                                ->icon('heroicon-m-phone')
                                ->color('success')
                                ->copyable()
                                ->copyMessage('Nomor berhasil disalin'),
                        ]),
                    ]),

                InfoSection::make('Pemetaan Alamat')
                    ->schema([
                        TextEntry::make('alamat')
                            ->label('Alamat Lengkap')
                            ->icon('heroicon-m-map-pin')
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'italic text-gray-500']),

                        TextEntry::make('map_url')
                            ->label('Akses Navigasi')
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

    /**
     * Pendaftaran rute halaman resource
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTitikPenyalurans::route('/'),
            'create' => Pages\CreateTitikPenyaluran::route('/create'),
            'edit' => Pages\EditTitikPenyaluran::route('/{record}/edit'),
        ];
    }


    public static function canCreate(): bool
    {
        return auth()->user()->role !== 'pemerintah';
    }

    public static function canEdit(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->role !== 'pemerintah';
    }

    public static function canDelete(\Illuminate\Database\Eloquent\Model $record): bool
    {
        return auth()->user()->role !== 'pemerintah';
    }
}