<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Grid as InfolistGrid;
use Illuminate\Support\Facades\Hash;

/**
 * Pengaturan Resource Manajemen Pengguna
 * Mengelola data autentikasi hak akses dan profil akun dalam sistem
 */
class UserResource extends Resource
{
    // Model referensi data pengguna
    protected static ?string $model = User::class;

    // Konfigurasi ikon navigasi pada bilah sisi
    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    // Label navigasi yang tampil pada menu sidebar
    protected static ?string $navigationLabel = 'Manajemen Pengguna';

    // Pengelompokan menu dalam kategori pengaturan sistem
    protected static ?string $navigationGroup = 'Pengaturan Sistem';

    /**
     * 🛡️ KUNCI PINTU UTAMA: 
     * Membatasi hak akses resource HANYA untuk peran super_admin.
     * Menu ini otomatis HILANG untuk Pemerintah dan Petugas!
     */
    public static function canViewAny(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user->role === 'super_admin';
    }

    /**
     * Definisi skema formulir pengelolaan akun pengguna
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pengguna')->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->label('Nama Lengkap'),
                        
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->label('Alamat Email'),
                        
                    TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->maxLength(255)
                        ->label('Kata Sandi')
                        ->placeholder('Kosongkan jika tidak ada perubahan'),
                        
                    Select::make('role')
                        ->options([
                            'super_admin' => 'Super Admin Dinas',
                            'petugas' => 'Petugas Lapangan',
                            'pemerintah' => 'Pemerintah Eksekutif',
                            'masyarakat' => 'Masyarakat Umum',
                        ])
                        ->required()
                        ->default('masyarakat')
                        ->label('Hak Akses Role'),
                ])->columns(2)
            ]);
    }

    /**
     * Definisi struktur tabel daftar seluruh pengguna
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Profil')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=FFFFFF&background=0284c7'),

                TextColumn::make('name')
                    ->label('Nama Pengguna')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                TextColumn::make('email')
                    ->label('Alamat Email')
                    ->searchable()
                    ->icon('heroicon-m-envelope'),
                    
                TextColumn::make('role')
                    ->label('Hak Akses')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'petugas' => 'warning',
                        'pemerintah' => 'info',
                        'masyarakat' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'Super Admin',
                        'petugas' => 'Petugas Lapangan',
                        'pemerintah' => 'Pemerintah Eksekutif',
                        'masyarakat' => 'Masyarakat',
                        default => 'Tidak Diketahui',
                    }),
                    
                TextColumn::make('created_at')
                    ->label('Tanggal Terdaftar')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->filters([
                // Filter dapat ditambahkan sesuai kebutuhan manajemen
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()->label('Lihat Detail'),
                    EditAction::make()->label('Ubah Akun'),
                    DeleteAction::make()->label('Hapus Akses'),
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
     * Tampilan informasi profil dan detail teknis akun
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Profil Pengguna')
                    ->schema([
                        ImageEntry::make('avatar')
                            ->hiddenLabel()
                            ->circular()
                            ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=FFFFFF&background=0284c7')
                            ->size(120)
                            ->alignCenter()
                            ->columnSpanFull(),
                            
                        TextEntry::make('name')
                            ->hiddenLabel()
                            ->weight('bold')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->alignCenter()
                            ->columnSpanFull(),
                    ]),

                InfoSection::make('Detail Keamanan Akun')
                    ->schema([
                        InfolistGrid::make(3)->schema([
                            TextEntry::make('email')
                                ->label('Alamat Email Aktif')
                                ->icon('heroicon-m-envelope'),
                                
                            TextEntry::make('role')
                                ->label('Tingkat Hak Akses')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'super_admin' => 'danger',
                                    'petugas' => 'warning',
                                    'pemerintah' => 'info',
                                    'masyarakat' => 'success',
                                    default => 'gray',
                                })
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'super_admin' => 'Super Admin',
                                    'petugas' => 'Petugas Lapangan',
                                    'pemerintah' => 'Pemerintah Eksekutif',
                                    'masyarakat' => 'Masyarakat',
                                    default => 'Tidak Diketahui',
                                }),
                                
                            TextEntry::make('created_at')
                                ->label('Bergabung Sejak')
                                ->date('d F Y')
                                ->icon('heroicon-m-calendar-days'),
                        ]),
                    ])
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}