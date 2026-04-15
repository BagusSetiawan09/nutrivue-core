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

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    
    protected static ?string $navigationLabel = 'Manajemen Pengguna';

    protected static ?string $navigationGroup = 'Pengaturan Sistem';

    public static function canViewAny(): bool
    {
        /** @var \App\Models\User $user */
        $user = auth()->user();
        return $user->role === 'super_admin';
    }

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
                        ->unique(ignoreRecord: true),
                        
                    TextInput::make('password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->maxLength(255)
                        ->label('Password (Kosongkan jika tidak ingin mengubah)'),
                        
                    Select::make('role')
                        ->options([
                            'super_admin' => 'Super Admin (Dinas)',
                            'petugas' => 'Petugas Lapangan',
                            'masyarakat' => 'Masyarakat Umum',
                        ])
                        ->required()
                        ->default('masyarakat')
                        ->label('Hak Akses (Role)'),
                ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('Profil')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&color=FFFFFF&background=0284c7'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->label('Nama Pengguna'),
                    
                TextColumn::make('email')
                    ->searchable()
                    ->icon('heroicon-m-envelope')
                    ->label('Alamat Email'),
                    
                TextColumn::make('role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'super_admin' => 'danger',
                        'petugas' => 'warning',
                        'masyarakat' => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'super_admin' => 'Super Admin',
                        'petugas' => 'Petugas Lapangan',
                        'masyarakat' => 'Masyarakat',
                        default => 'Unknown',
                    })
                    ->label('Hak Akses'),
                    
                TextColumn::make('created_at')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->label('Tanggal Terdaftar'),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
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

                InfoSection::make('Detail Akun')
                    ->schema([
                        InfolistGrid::make(3)->schema([
                            TextEntry::make('email')
                                ->label('Alamat Email')
                                ->icon('heroicon-m-envelope'),
                                
                            TextEntry::make('role')
                                ->label('Hak Akses')
                                ->badge()
                                ->color(fn (string $state): string => match ($state) {
                                    'super_admin' => 'danger',
                                    'petugas' => 'warning',
                                    'masyarakat' => 'success',
                                    default => 'gray',
                                })
                                ->formatStateUsing(fn (string $state): string => match ($state) {
                                    'super_admin' => 'Super Admin',
                                    'petugas' => 'Petugas Lapangan',
                                    'masyarakat' => 'Masyarakat',
                                    default => 'Unknown',
                                }),
                                
                            TextEntry::make('created_at')
                                ->label('Terdaftar Sejak')
                                ->date('d F Y')
                                ->icon('heroicon-m-calendar-days'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}