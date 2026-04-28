<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ClientAppResource\Pages;
use App\Models\ClientApp;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfoSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Illuminate\Support\Str;

/**
 * Pengaturan Resource Aplikasi Klien
 * Mengelola akses API Token untuk integrasi platform eksternal
 */
class ClientAppResource extends Resource
{
    // Model referensi data aplikasi klien
    protected static ?string $model = ClientApp::class;

    // Konfigurasi ikon navigasi bilah sisi
    protected static ?string $navigationIcon = 'heroicon-o-key';

    // Label navigasi yang tampil pada menu
    protected static ?string $navigationLabel = 'Manajemen API Keys';

    // Pengelompokan menu dalam navigasi sistem
    protected static ?string $navigationGroup = 'Pengaturan Sistem';

    /**
     * Definisi skema formulir pembuatan dan pembaruan data
     */
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Aplikasi Klien')
                    ->description('Daftarkan aplikasi klien seperti Android iOS atau Web untuk integrasi API')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Aplikasi Identitas')
                            ->placeholder('Contoh Aplikasi Android Warga v1.0')
                            ->required()
                            ->maxLength(255),
                            
                        Textarea::make('description')
                            ->label('Deskripsi Penggunaan')
                            ->placeholder('Jelaskan tujuan penggunaan token ini')
                            ->maxLength(500),
                            
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Nonaktifkan untuk mencabut seluruh akses API klien ini')
                            ->onColor('success')
                            ->offColor('danger'),
                    ])->columns(1)
            ]);
    }

    /**
     * Definisi struktur tabel daftar aplikasi klien
     */
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Klien')
                    ->searchable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('tokens_count')
                    ->label('Token Aktif')
                    ->counts('tokens')
                    ->badge()
                    ->color('info'),
                    
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Akses Aktif')
                    ->onColor('success')
                    ->offColor('danger'),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Didaftarkan Pada')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                // Filter data dapat ditambahkan di sini
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('Lihat Detail'), //  TAMBAHAN VIEW ACTION
                    Tables\Actions\EditAction::make()->label('Ubah Data'),
                    
                    // Aksi untuk pembuatan token API baru
                    Tables\Actions\Action::make('generate_token')
                        ->label('Generate Token Baru')
                        ->icon('heroicon-m-sparkles')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Generate API Token Baru')
                        ->modalDescription('Pastikan klien dalam status aktif sebelum membuat token baru')
                        ->modalSubmitActionLabel('Ya Generate')
                        ->action(function (ClientApp $record) {
                            if (!$record->is_active) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Gagal')
                                    ->body('Aktifkan klien terlebih dahulu')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Proses pembuatan token menggunakan Laravel Sanctum
                            $tokenName = 'Token-' . Str::random(6);
                            $token = $record->createToken($tokenName);

                            \Filament\Notifications\Notification::make()
                                ->title('Token Berhasil Dibuat')
                                ->body("API Token Anda **{$token->plainTextToken}** Simpan token ini dengan aman karena tidak akan ditampilkan kembali")
                                ->success()
                                ->persistent()
                                ->send();
                        }),
                        
                    // Aksi untuk mencabut seluruh akses token klien
                    Tables\Actions\Action::make('revoke_tokens')
                        ->label('Cabut Semua Token')
                        ->icon('heroicon-m-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Cabut Akses API')
                        ->modalDescription('Tindakan ini akan menghapus seluruh token aktif secara permanen')
                        ->action(function (ClientApp $record) {
                            $record->tokens()->delete();
                            \Filament\Notifications\Notification::make()
                                ->title('Berhasil')
                                ->body('Seluruh token klien telah dicabut')
                                ->success()
                                ->send();
                        }),
                ])->button()->label('Kelola API')->color('gray')
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     *  TAMBAHAN INFOLIST: Tampilan detail aplikasi klien yang elegan
     */
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfoSection::make('Detail Klien API')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Nama Aplikasi Identitas')
                            ->weight('bold')
                            ->size(TextEntry\TextEntrySize::Large),
                            
                        IconEntry::make('is_active')
                            ->label('Status Operasional')
                            ->boolean(),
                            
                        TextEntry::make('description')
                            ->label('Deskripsi Penggunaan')
                            ->columnSpanFull()
                            ->prose(),
                            
                        TextEntry::make('created_at')
                            ->label('Tanggal Registrasi')
                            ->dateTime()
                            ->badge()
                            ->color('gray'),
                    ])->columns(2)
            ]);
    }

    /**
     * Pendaftaran rute halaman resource
     */
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientApps::route('/'),
            'create' => Pages\CreateClientApp::route('/create'),
            'edit' => Pages\EditClientApp::route('/{record}/edit'),
        ];
    }
}