<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReviewResource\Pages;
use App\Models\Review;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Grid as InfolistGrid;

/**
 * Pengaturan Resource Ulasan Publik
 * Mengelola moderasi komentar dan penilaian dari warga atau pengguna aplikasi
 */
class ReviewResource extends Resource
{
    // Model referensi data ulasan
    protected static ?string $model = Review::class;

    // Konfigurasi ikon navigasi bilah sisi
    protected static ?string $navigationIcon = 'heroicon-o-star';
    
    // Label navigasi yang tampil pada menu sidebar
    protected static ?string $navigationLabel = 'Ulasan Publik';

    /**
     * Membatasi visibilitas menu hanya untuk super admin dan petugas
     */
    public static function canViewAny(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return in_array($user->role, ['super_admin', 'petugas']);
    }

    /**
     * Membatasi pembuatan ulasan manual hanya untuk peran super admin
     */
    public static function canCreate(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user->role === 'super_admin';
    }

    /**
     * Memberikan hak moderasi ulasan kepada super admin dan petugas
     */
    public static function canEdit($record): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return in_array($user->role, ['super_admin', 'petugas']);
    }

    /**
     * Membatasi hak penghapusan ulasan secara massal hanya untuk super admin
     */
    public static function canDeleteAny(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user->role === 'super_admin';
    }

    /**
     * Membatasi hak penghapusan ulasan satuan hanya untuk super admin
     */
    public static function canDelete($record): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user->role === 'super_admin';
    }

    /**
     * Definisi skema formulir moderasi ulasan
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\Select::make('menu_id')
                        ->relationship('menu', 'nama_menu')
                        ->label('Menu Makanan')
                        ->searchable()
                        ->required(),
                        
                    Forms\Components\Select::make('user_id')
                        ->relationship('user', 'name')
                        ->label('Nama Warga Pengguna')
                        ->searchable()
                        ->required(),
                        
                    Forms\Components\Select::make('rating')
                        ->label('Penilaian Bintang')
                        ->options([
                            1 => '⭐ 1 Bintang',
                            2 => '⭐⭐ 2 Bintang',
                            3 => '⭐⭐⭐ 3 Bintang',
                            4 => '⭐⭐⭐⭐ 4 Bintang',
                            5 => '⭐⭐⭐⭐⭐ 5 Bintang',
                        ])
                        ->required(),
                        
                    Forms\Components\Textarea::make('komentar')
                        ->label('Isi Komentar')
                        ->required()
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('foto_bukti')
                        ->label('Foto Bukti Opsional')
                        ->image()
                        ->directory('review-evidence')
                        ->maxSize(5120)
                        ->helperText('Lampirkan foto jika ditemukan ketidaksesuaian pada menu')
                        ->columnSpanFull(),
                        
                    Forms\Components\Toggle::make('is_visible')
                        ->label('Tampilkan ke Publik')
                        ->helperText('Nonaktifkan jika komentar mengandung unsur pelanggaran')
                        ->default(true)
                        ->onColor('success')
                        ->offColor('danger'),
                ])->columns(2)
            ]);
    }

    /**
     * Definisi struktur tabel daftar ulasan warga
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto_bukti')
                    ->label('Bukti')
                    ->circular()
                    ->defaultImageUrl('https://ui-avatars.com/api/?name=No+Photo&color=FFFFFF&background=9CA3AF'),

                TextColumn::make('user.name')
                    ->label('Nama Warga')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                TextColumn::make('menu.nama_menu')
                    ->label('Menu Terulas')
                    ->searchable()
                    ->limit(20),
                    
                TextColumn::make('rating')
                    ->label('Rating')
                    ->badge()
                    ->color('warning')
                    ->icon('heroicon-s-star')
                    ->formatStateUsing(fn ($state) => $state . ' Bintang')
                    ->sortable(),
                    
                TextColumn::make('komentar')
                    ->label('Komentar')
                    ->limit(30)
                    ->searchable(),
                    
                ToggleColumn::make('is_visible')
                    ->label('Publikasi')
                    ->onColor('success')
                    ->offColor('danger'),
                    
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                // Filter tambahan dapat ditambahkan di sini
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label('Lihat'),
                    EditAction::make()->label('Moderasi'),
                    DeleteAction::make()->label('Hapus'),
                ])
                ->button()
                ->color('gray')
                ->label('Aksi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Tampilan detail ulasan dan informasi moderasi
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Detail Ulasan Publik')
                    ->schema([
                        InfolistGrid::make(2)->schema([
                            TextEntry::make('user.name')
                                ->label('Pengirim Ulasan')
                                ->weight('bold')
                                ->size(TextEntry\TextEntrySize::Large)
                                ->icon('heroicon-m-user-circle')
                                ->iconColor('primary'),
                                
                            TextEntry::make('menu.nama_menu')
                                ->label('Menu Makanan')
                                ->icon('heroicon-m-rectangle-stack'),
                        ]),
                        
                        InfolistGrid::make(2)->schema([
                            TextEntry::make('rating')
                                ->label('Penilaian')
                                ->badge()
                                ->color('warning')
                                ->icon('heroicon-s-star')
                                ->formatStateUsing(fn ($state) => $state . ' Bintang')
                                ->size(TextEntry\TextEntrySize::Large),
                                
                            TextEntry::make('created_at')
                                ->label('Waktu Ulasan')
                                ->date('d F Y H:i')
                                ->icon('heroicon-m-clock'),
                        ]),
                        
                        TextEntry::make('komentar')
                            ->label('Isi Komentar')
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'bg-gray-50 dark:bg-white/5 p-4 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10 italic']),

                        ImageEntry::make('foto_bukti')
                            ->label('Foto Bukti Lapangan')
                            ->hidden(fn ($state) => $state === null) 
                            ->size(400)
                            ->alignCenter()
                            ->columnSpanFull()
                            ->extraAttributes(['class' => 'mt-4 p-4 rounded-xl border border-gray-200 dark:border-white/10 bg-gray-50 dark:bg-white/5 flex justify-center']),
                            
                        TextEntry::make('is_visible')
                            ->label('Status Moderasi')
                            ->badge()
                            ->color(fn (bool $state): string => $state ? 'success' : 'danger')
                            ->formatStateUsing(fn (bool $state): string => $state ? 'Ditampilkan ke Publik' : 'Disembunyikan Moderasi')
                            ->icon(fn (bool $state): string => $state ? 'heroicon-m-eye' : 'heroicon-m-eye-slash')
                            ->columnSpanFull(),
                    ])
            ]);
    }

    /**
     * Definisi relasi antar model
     */
    public static function getRelations(): array
    {
        return [
            // Relasi tambahan dapat didefinisikan di sini
        ];
    }

    /**
     * Pendaftaran rute halaman resource
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListReviews::route('/'),
            'create' => Pages\CreateReview::route('/create'),
            'edit' => Pages\EditReview::route('/{record}/edit'),
        ];
    }
}