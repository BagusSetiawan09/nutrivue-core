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

class MenuResource extends Resource
{
    protected static ?string $model = Menu::class;

    // Ikon untuk di sidebar kiri
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    
    // Nama menu di sidebar
    protected static ?string $navigationLabel = 'Jadwal Menu MBG';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Card::make()->schema([
                    Forms\Components\TextInput::make('nama_menu')
                        ->required()
                        ->maxLength(255),
                    Forms\Components\DatePicker::make('tanggal_distribusi')
                        ->required(),
                    Forms\Components\Select::make('target_penerima')
                        ->options([
                            'Siswa' => 'Anak Sekolah',
                            'Balita' => 'Balita',
                            'Ibu Hamil' => 'Ibu Hamil',
                        ])
                        ->required(),
                    Forms\Components\TextInput::make('lokasi_distribusi')
                        ->label('Titik Penyaluran (Sekolah/Posyandu)')
                        ->maxLength(255),
                    Forms\Components\FileUpload::make('foto_makanan')
                        ->image()
                        ->directory('menu-images')
                        ->maxSize(5120)
                        ->columnSpanFull(),
                    Forms\Components\Textarea::make('deskripsi')
                        ->label('Deskripsi/Kandungan Menu')
                        ->columnSpanFull(),
                        
                    // Bagian untuk input nilai gizi dengan styling khusus
                    Forms\Components\Grid::make(4)->schema([
                        Forms\Components\TextInput::make('kalori')
                            ->numeric()
                            ->suffix('Kcal'),
                        Forms\Components\TextInput::make('protein')
                            ->numeric()
                            ->suffix('g'),
                        Forms\Components\TextInput::make('karbohidrat')
                            ->numeric()
                            ->suffix('g'),
                        Forms\Components\TextInput::make('lemak')
                            ->numeric()
                            ->suffix('g'),
                    ]),

                    Forms\Components\Select::make('status')
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto_makanan')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->nama_menu) . '&color=FFFFFF&background=9CA3AF'),
                
                Tables\Columns\TextColumn::make('nama_menu')
                    ->searchable()
                    ->weight('bold'),
                
                // Menambahkan kolom status dengan badge dan ikon untuk visualisasi
                Tables\Columns\TextColumn::make('status')
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
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Siswa' => 'success',
                        'Balita' => 'warning',
                        'Ibu Hamil' => 'info',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('tanggal_distribusi')
                    ->date('d M Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('lokasi_distribusi')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    
                    // Tambahan: Action untuk mengubah status langsung dari tabel tanpa masuk ke halaman edit
                    Action::make('ubah_status')
                        ->label('Ubah Status')
                        ->icon('heroicon-m-arrow-path')
                        ->color('info') 
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
                        ->successNotificationTitle('Status berhasil diperbarui!'),
                        
                    DeleteAction::make(),
                ])
                ->button()
                ->color('gray')
                ->label('Aksi'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // Bagian 1: Foto Makanan dan Nama Menu dengan styling khusus
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

                // Bagian 2: Informasi Distribusi dengan ikon dan badge untuk target penerima
                Section::make('Informasi Distribusi')
                    ->schema([
                        TextEntry::make('tanggal_distribusi')
                            ->label('Tanggal')
                            ->date('d F Y')
                            ->icon('heroicon-m-calendar'),
                            
                        TextEntry::make('target_penerima')
                            ->label('Target')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Siswa' => 'success',
                                'Balita' => 'warning',
                                'Ibu Hamil' => 'info',
                                default => 'gray',
                            }),
                            
                        TextEntry::make('lokasi_distribusi')
                            ->label('Titik Lokasi')
                            ->icon('heroicon-m-map-pin'),
                            
                        TextEntry::make('deskripsi')
                            ->columnSpanFull()
                            ->color('gray'),
                    ])->columns(3),
                
                // Bagian 3: Kandungan Gizi & Makronutrien dengan styling khusus
                Section::make('Kandungan Gizi & Makronutrien')
                    ->schema([
                        InfolistGrid::make(3)->schema([
                            
                            // Menampilkan nilai gizi dengan styling
                            InfolistGrid::make(1)->schema([
                                TextEntry::make('kalori')->suffix(' Kcal')
                                    ->size(TextEntry\TextEntrySize::Large)
                                    ->weight('bold')->color('danger')
                                    ->extraAttributes(['class' => 'bg-gray-50 dark:bg-white/5 p-4 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10']),
                                    
                                TextEntry::make('protein')->suffix(' g')
                                    ->size(TextEntry\TextEntrySize::Large)->weight('bold')
                                    ->extraAttributes(['class' => 'bg-gray-50 dark:bg-white/5 p-4 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10']),
                                    
                                TextEntry::make('karbohidrat')->suffix(' g')
                                    ->size(TextEntry\TextEntrySize::Large)->weight('bold')
                                    ->extraAttributes(['class' => 'bg-gray-50 dark:bg-white/5 p-4 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10']),
                                    
                                TextEntry::make('lemak')->suffix(' g')
                                    ->size(TextEntry\TextEntrySize::Large)->weight('bold')
                                    ->extraAttributes(['class' => 'bg-gray-50 dark:bg-white/5 p-4 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10']),
                            ])->columnSpan(1),
                            
                            // Chart Gizi dibuat dalam ViewEntry yang memanggil Blade Component
                            ViewEntry::make('gizi_chart')
                                ->view('filament.infolists.components.gizi-chart')
                                ->columnSpan(2),
                        ]),
                    ])
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenus::route('/'),
            'create' => Pages\CreateMenu::route('/create'),
            'edit' => Pages\EditMenu::route('/{record}/edit'),
        ];
    }
}