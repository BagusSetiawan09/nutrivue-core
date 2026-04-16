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
use Illuminate\Support\Str;

class ClientAppResource extends Resource
{
    protected static ?string $model = ClientApp::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationLabel = 'Manajemen API Keys';
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
                Section::make('Informasi Aplikasi Klien')
                    ->description('Daftarkan aplikasi (misal: Android, iOS, Web) yang akan menggunakan API sistem ini.')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Aplikasi / Identitas')
                            ->placeholder('Contoh: Aplikasi Android Warga v1.0')
                            ->required()
                            ->maxLength(255),
                            
                        Textarea::make('description')
                            ->label('Deskripsi Penggunaan')
                            ->placeholder('Jelaskan untuk apa token ini digunakan.')
                            ->maxLength(500),
                            
                        Toggle::make('is_active')
                            ->label('Status Aktif')
                            ->default(true)
                            ->helperText('Matikan jika kamu ingin mencabut akses API untuk klien ini.')
                            ->onColor('success')
                            ->offColor('danger'),
                    ])->columns(1)
            ]);
    }

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
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    
                    Tables\Actions\Action::make('generate_token')
                        ->label('Generate Token Baru')
                        ->icon('heroicon-m-sparkles')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Generate API Token Baru')
                        ->modalDescription('Apakah kamu yakin ingin membuat token baru untuk klien ini?')
                        ->modalSubmitActionLabel('Ya, Generate')
                        ->action(function (ClientApp $record, Tables\Actions\Action $action) {
                            if (!$record->is_active) {
                                \Filament\Notifications\Notification::make()
                                    ->title('Gagal')
                                    ->body('Aktifkan klien terlebih dahulu sebelum membuat token.')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            // Generate Token menggunakan Sanctum
                            $tokenName = 'Token-' . Str::random(6);
                            $token = $record->createToken($tokenName);

                            \Filament\Notifications\Notification::make()
                                ->title('Token Berhasil Dibuat!')
                                ->body("API Token Anda: **{$token->plainTextToken}** <br><br> _(Simpan token ini baik-baik! Token tidak akan ditampilkan lagi setelah Anda menutup pesan ini.)_")
                                ->success()
                                ->persistent()
                                ->send();
                        }),
                        
                    Tables\Actions\Action::make('revoke_tokens')
                        ->label('Cabut Semua Token')
                        ->icon('heroicon-m-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Cabut Akses API')
                        ->modalDescription('Tindakan ini akan menghapus semua token aktif. Aplikasi klien akan langsung terputus dari sistem. Yakin?')
                        ->action(function (ClientApp $record) {
                            $record->tokens()->delete();
                            \Filament\Notifications\Notification::make()
                                ->title('Berhasil')
                                ->body('Semua token untuk klien ini telah dicabut.')
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClientApps::route('/'),
            'create' => Pages\CreateClientApp::route('/create'),
            'edit' => Pages\EditClientApp::route('/{record}/edit'),
        ];
    }
}