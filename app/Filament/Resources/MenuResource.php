<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuResource\Pages;
use App\Models\Menu;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\Action; 
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Filament\Infolists\Components\ViewEntry;

/**
 * Pengaturan Resource Menu Makanan
 * Mengelola jadwal distribusi makanan bergizi serta kandungan nutrisinya
 */
class MenuResource extends Resource
{
    // Model referensi data menu makanan
    protected static ?string $model = Menu::class;

    // Konfigurasi ikon navigasi bilah sisi
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    // Label navigasi yang tampil pada menu sidebar
    protected static ?string $navigationLabel = 'Jadwal Menu MBG';

    public static function getNavigationGroup(): ?string
    {
        return auth()->user()->role === 'pemerintah' 
            ? 'Laporan Eksekutif' 
            : 'Distribusi Makanan';
    }

    /**
     * Definisi skema formulir input data menu
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()->schema([
                    Forms\Components\TextInput::make('nama_menu')
                        ->label('Nama Menu')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\DatePicker::make('tanggal_distribusi')
                        ->label('Tanggal Distribusi')
                        ->required(),

                    Forms\Components\Select::make('target_penerima')
                        ->label('Target Penerima')
                        ->options([
                            'Siswa' => 'Anak Sekolah',
                            'Balita' => 'Balita',
                            'Ibu Hamil' => 'Ibu Hamil',
                        ])
                        ->required(),

                    Forms\Components\Select::make('titik_penyaluran_id')
                        ->relationship('titik_penyaluran', 'nama_lokasi')
                        ->label('Lokasi Penyaluran')
                        ->searchable()
                        ->preload()
                        ->required(),

                    Forms\Components\FileUpload::make('foto_makanan')
                        ->label('Foto Makanan')
                        ->image()
                        ->directory('menu-images')
                        ->maxSize(5120)
                        ->columnSpanFull(),

                    Forms\Components\Textarea::make('deskripsi')
                        ->label('Deskripsi Kandungan Menu')
                        ->columnSpanFull(),
                        
                    // Skema input informasi nilai gizi
                    Forms\Components\Grid::make(4)->schema([
                        Forms\Components\TextInput::make('kalori')
                            ->label('Kalori')
                            ->numeric()
                            ->suffix('Kcal'),
                        Forms\Components\TextInput::make('protein')
                            ->label('Protein')
                            ->numeric()
                            ->suffix('g'),
                        Forms\Components\TextInput::make('karbohidrat')
                            ->label('Karbohidrat')
                            ->numeric()
                            ->suffix('g'),
                        Forms\Components\TextInput::make('lemak')
                            ->label('Lemak')
                            ->numeric()
                            ->suffix('g'),
                    ]),

                    Forms\Components\Select::make('status')
                        ->label('Status Distribusi')
                        ->options([
                            'Menunggu' => 'Menunggu Jadwal',
                            'Sedang Dikirim' => 'Sedang Dikirim',
                            'Selesai' => 'Selesai Distribusi',
                        ])
                        ->default('Menunggu')
                        ->required(),
                ])
            ]);
    }

    /**
     * Definisi struktur tabel daftar menu distribusi
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto_makanan')
                    ->label('Foto')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->nama_menu) . '&color=FFFFFF&background=9CA3AF'),
                
                Tables\Columns\TextColumn::make('nama_menu')
                    ->label('Nama Menu')
                    ->searchable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Menunggu' => 'gray',
                        'Sedang Dikirim' => 'warning',
                        'Selesai' => 'success',
                        default => 'primary',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Menunggu' => 'heroicon-m-clock',
                        'Sedang Dikirim' => 'heroicon-m-truck',
                        'Selesai' => 'heroicon-m-check-circle',
                        default => 'heroicon-m-information-circle',
                    }),

                Tables\Columns\TextColumn::make('target_penerima')
                    ->label('Target')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Siswa' => 'success',
                        'Balita' => 'warning',
                        'Ibu Hamil' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('tanggal_distribusi')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('titik_penyaluran.nama_lokasi')
                    ->label('Lokasi Penyaluran')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label('Lihat Detail'),
                    EditAction::make()->label('Ubah Data'),
                    
                    Action::make('ubah_status')
                        ->label('Ubah Status')
                        ->icon('heroicon-m-arrow-path')
                        ->color('info') 
                        ->visible(fn () => auth()->user()->role !== 'pemerintah')
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label('Pilih Status Terbaru')
                                ->options([
                                    'Menunggu' => 'Menunggu Jadwal',
                                    'Sedang Dikirim' => 'Sedang Dikirim',
                                    'Selesai' => 'Selesai Distribusi',
                                ])
                                ->default(fn ($record) => $record->status)
                                ->required(),
                        ])
                        ->action(function ($record, array $data): void {
                            $record->update(['status' => $data['status']]);
                        })
                        ->successNotificationTitle('Status distribusi berhasil diperbarui'),
                        
                    DeleteAction::make()->label('Hapus'),
                ])
                ->button()
                ->color('gray')
                ->label('Kelola'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Tampilan detail informasi menu makanan
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->schema([
                        ImageEntry::make('foto_makanan')
                            ->hiddenLabel()
                            ->circular()
                            ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->nama_menu) . '&color=FFFFFF&background=9CA3AF')
                            ->size(120) 
                            ->alignCenter()
                            ->columnSpanFull(),
                            
                        TextEntry::make('nama_menu')
                            ->hiddenLabel()
                            ->weight('bold')
                            ->size(TextEntry\TextEntrySize::Large) 
                            ->alignCenter()
                            ->columnSpanFull(),
                    ]),

                Section::make('Informasi Distribusi')
                    ->schema([
                        TextEntry::make('tanggal_distribusi')
                            ->label('Tanggal Distribusi')
                            ->date('d F Y')
                            ->icon('heroicon-m-calendar'),
                            
                        TextEntry::make('target_penerima')
                            ->label('Target Penerima')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Siswa' => 'success',
                                'Balita' => 'warning',
                                'Ibu Hamil' => 'info',
                                default => 'gray',
                            }),
                            
                        TextEntry::make('titik_penyaluran.nama_lokasi')
                            ->label('Titik Lokasi')
                            ->icon('heroicon-m-map-pin'),
                            
                        TextEntry::make('deskripsi')
                            ->label('Deskripsi Menu')
                            ->columnSpanFull()
                            ->color('gray'),
                    ])->columns(3),
                
                Section::make('Kandungan Gizi Makronutrien')
                    ->schema([
                        InfolistGrid::make(3)->schema([
                            
                            // Visualisasi nilai numerik gizi
                            InfolistGrid::make(1)->schema([
                                TextEntry::make('kalori')->suffix(' Kcal')
                                    ->label('Energi')
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold')->color('danger')
                                    ->extraAttributes(['class' => 'bg-gray-50 dark:bg-white/5 p-4 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10']),
                                    
                                TextEntry::make('protein')->suffix(' g')
                                    ->label('Protein')
                                    ->size(TextEntry\TextEntrySize::Large)->weight('bold')
                                    ->extraAttributes(['class' => 'bg-gray-50 dark:bg-white/5 p-4 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10']),
                                    
                                TextEntry::make('karbohidrat')->suffix(' g')
                                    ->label('Karbohidrat')
                                    ->size(TextEntry\TextEntrySize::Large)->weight('bold')
                                    ->extraAttributes(['class' => 'bg-gray-50 dark:bg-white/5 p-4 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10']),
                                    
                                TextEntry::make('lemak')->suffix(' g')
                                    ->label('Lemak')
                                    ->size(TextEntry\TextEntrySize::Large)->weight('bold')
                                    ->extraAttributes(['class' => 'bg-gray-50 dark:bg-white/5 p-4 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10']),
                            ])->columnSpan(1),
                            
                            // Komponen visualisasi grafik gizi
                            ViewEntry::make('gizi_chart')
                                ->label('Grafik Komposisi Gizi')
                                ->view('filament.infolists.components.gizi-chart')
                                ->columnSpan(2),
                        ]),
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
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }

    // FILTER ISOLASI DATA MENU DISTRIBUSI
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $query = parent::getEloquentQuery();
        
        // Jika yang login bukan Super Admin, tampilkan HANYA jadwal menu buatan dia sendiri
        if (auth()->user()->role !== 'super_admin') {
            $query->where('created_by', auth()->id());
        }
        
        return $query;
    }
}