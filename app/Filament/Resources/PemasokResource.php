<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PemasokResource\Pages;
use App\Models\Pemasok;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;

class PemasokResource extends Resource
{
    protected static ?string $model = Pemasok::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationLabel = 'Direktori Pemasok';
    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Usaha')->schema([
                    Forms\Components\TextInput::make('nama_usaha')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('nama_pemilik')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\TextInput::make('no_wa')
                        ->tel()
                        ->required()
                        ->maxLength(255)
                        ->label('Nomor WhatsApp'),
                    Forms\Components\TextInput::make('email')
                        ->email()
                        ->maxLength(255),
                    Forms\Components\Textarea::make('alamat')
                        ->required()
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('kapasitas_produksi_harian')
                        ->numeric()
                        ->default(0)
                        ->suffix('Porsi / Hari')
                        ->required(),
                ])->columns(2),

                //  FITUR: REPEATER BAHAN BAKU DINAMIS
                Forms\Components\Section::make('Kapasitas Bahan Baku Harian')->schema([
                    Forms\Components\Repeater::make('bahan_baku_tersedia')
                        ->label('Daftar Ketersediaan Bahan')
                        ->addActionLabel('Tambah Bahan Baku')
                        ->schema([
                            Forms\Components\TextInput::make('nama_bahan')
                                ->label('Nama Bahan (Cth: Daging Sapi)')
                                ->required(),
                            Forms\Components\TextInput::make('kuantitas')
                                ->label('Kapasitas Harian')
                                ->numeric()
                                ->required(),
                            Forms\Components\Select::make('satuan')
                                ->label('Satuan')
                                ->options([
                                    'Kg' => 'Kilogram (Kg)',
                                    'Liter' => 'Liter (L)',
                                    'Ikat' => 'Ikat',
                                    'Butir' => 'Butir',
                                    'Pack' => 'Pack',
                                ])
                                ->default('Kg')
                                ->required(),
                        ])
                        ->columns(3)
                        ->columnSpanFull()
                ]),

                Forms\Components\Section::make('Sertifikasi & Survey')->schema([
                    Forms\Components\Toggle::make('is_halal')
                        ->label('Memiliki Sertifikat Halal MUI/BPJPH')
                        ->live()
                        ->onColor('success'),
                    Forms\Components\TextInput::make('no_sertifikat_halal')
                        ->label('Nomor Sertifikat')
                        ->visible(fn (Forms\Get $get) => $get('is_halal')),
                        
                    //  FITUR: UPLOAD BUKTI HALAL
                    Forms\Components\FileUpload::make('file_sertifikat_halal')
                        ->label('Unggah Foto/Dokumen Sertifikat Halal')
                        ->directory('pemasok-sertifikat')
                        ->visibility('public')
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->visible(fn (Forms\Get $get) => $get('is_halal'))
                        ->columnSpanFull(),
                        
                    Forms\Components\FileUpload::make('foto_dapur')
                        ->label('Foto Kondisi Dapur / Lokasi Masak')
                        ->image()
                        ->directory('pemasok-dapur')
                        ->visibility('public')
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('deskripsi')
                        ->label('Catatan Tambahan (Spesialisasi Menu, dll)')
                        ->columnSpanFull(),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_usaha')
                    ->searchable()
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('nama_pemilik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_wa')
                    ->icon('heroicon-m-phone')
                    ->copyable(),
                Tables\Columns\IconColumn::make('is_halal')
                    ->label('Halal')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\TextColumn::make('status_akun')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Dilaporkan Penipu' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_halal')
                    ->label('Status Halal')
                    ->boolean()
                    ->trueLabel('Tersertifikasi Halal')
                    ->falseLabel('Belum Tersertifikasi'),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('Lihat Detail'),
                    
                    // Edit & Delete otomatis disembunyikan oleh Filament berkat Policy
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),

                    //  TOMBOL KHUSUS IT MBG: LAPORKAN PENIPU
                    Tables\Actions\Action::make('laporkan_penipu')
                        ->label('Laporkan Indikasi Penipuan')
                        ->icon('heroicon-m-exclamation-triangle')
                        ->color('danger')
                        ->visible(fn ($record) => auth()->user()->role === 'it_mbg' && $record->status_akun !== 'Dilaporkan Penipu')
                        ->requiresConfirmation()
                        ->modalHeading('Laporkan Pemasok Fiktif/Penipu')
                        ->modalDescription('Apakah Anda yakin pemasok ini melakukan kecurangan? Laporan ini akan diteruskan ke Super Admin untuk ditindaklanjuti.')
                        ->form([
                            Forms\Components\Textarea::make('alasan_laporan')
                                ->label('Sertakan Bukti / Alasan Temuan Lapangan')
                                ->required(),
                        ])
                        ->action(function (Pemasok $record, array $data) {
                            $record->update([
                                'status_akun' => 'Dilaporkan Penipu',
                                'alasan_laporan' => $data['alasan_laporan'],
                            ]);
                            \Filament\Notifications\Notification::make()
                                ->title('Laporan Berhasil Terkirim')
                                ->body('Akun ini telah ditandai merah. Menunggu eksekusi Super Admin.')
                                ->danger()
                                ->send();
                        }),
                ])->button()->color('gray')->label('Aksi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // ⚡ ZONA PERINGATAN DARURAT: PROFESIONAL & ELEGAN
                InfoSection::make('PERINGATAN DARURAT: PEMASOK DILAPORKAN')
                    ->icon('heroicon-m-exclamation-triangle')
                    ->iconColor('danger')
                    ->description('Pemasok ini telah ditandai oleh tim IT MBG lapangan karena terindikasi penipuan atau fiktif.')
                    ->schema([
                        TextEntry::make('status_akun')
                            ->label('Status Saat Ini')
                            ->badge()
                            ->color('danger')
                            ->size(TextEntry\TextEntrySize::Large),
                        TextEntry::make('alasan_laporan')
                            ->label('Bukti / Alasan Pelaporan')
                            ->color('danger')
                            ->weight('bold')
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'bg-danger-50 dark:bg-danger-500/10 p-4 rounded-xl ring-1 ring-danger-500/20 text-danger-600 dark:text-danger-400 italic']),
                    ])
                    ->columns(1)
                    ->visible(fn ($record) => $record->status_akun === 'Dilaporkan Penipu'),

                InfoSection::make('Detail Profil Pemasok')
                    ->schema([
                        ImageEntry::make('foto_dapur')
                            ->hiddenLabel()
                            ->disk('public')
                            ->size(300)
                            ->columnSpanFull()
                            ->alignCenter(),
                        TextEntry::make('nama_usaha')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->weight('bold')
                            ->columnSpanFull(),
                        TextEntry::make('nama_pemilik')
                            ->icon('heroicon-m-user'),
                        TextEntry::make('no_wa')
                            ->icon('heroicon-m-phone')
                            ->copyable(),
                        TextEntry::make('kapasitas_produksi_harian')
                            ->suffix(' Porsi / Hari')
                            ->badge()
                            ->color('info'),
                        TextEntry::make('alamat')
                            ->columnSpanFull()
                            ->icon('heroicon-m-map-pin'),
                    ])->columns(3),

                //  INFOLIST BARU: TAMPILAN BAHAN BAKU
                InfoSection::make('Kapasitas Bahan Baku Harian')
                    ->schema([
                        RepeatableEntry::make('bahan_baku_tersedia')
                            ->hiddenLabel()
                            ->schema([
                                TextEntry::make('nama_bahan')
                                    ->label('Bahan')
                                    ->weight('bold'),
                                TextEntry::make('kuantitas')
                                    ->label('Jumlah')
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('satuan')
                                    ->label('Satuan')
                                    ->color('gray'),
                            ])
                            ->columns(3)
                            ->columnSpanFull()
                    ]),

                InfoSection::make('Status Sertifikasi Halal')
                    ->schema([
                        IconEntry::make('is_halal')
                            ->label('Status Halal')
                            ->boolean(),
                        TextEntry::make('no_sertifikat_halal')
                            ->label('Nomor Sertifikat')
                            ->placeholder('Tidak ada data')
                            ->color('gray'),
                        TextEntry::make('file_sertifikat_halal')
                            ->label('Dokumen Bukti')
                            ->formatStateUsing(fn ($state) => 'Lihat Dokumen / Foto')
                            ->url(fn ($record) => $record->file_sertifikat_halal ? asset('storage/' . $record->file_sertifikat_halal) : null)
                            ->openUrlInNewTab()
                            ->color('primary')
                            ->icon('heroicon-m-document-text'),
                    ])->columns(3)
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPemasoks::route('/'),
            'create' => Pages\CreatePemasok::route('/create'),
            'edit' => Pages\EditPemasok::route('/{record}/edit'),
        ];
    }
}