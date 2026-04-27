<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    
    protected static ?string $navigationLabel = 'Manajemen FAQ';
    
    // ⚡ INI KUNCI OPSI A: Memasukkan menu ke grup Pengaturan Sistem
    protected static ?string $navigationGroup = 'Pengaturan Sistem';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Pertanyaan dan Jawaban')->schema([
                    Forms\Components\TextInput::make('question')
                        ->label('Pertanyaan')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    
                    Forms\Components\Textarea::make('answer')
                        ->label('Jawaban Lengkap')
                        ->required()
                        ->rows(5)
                        ->columnSpanFull(),
                        
                    Forms\Components\Toggle::make('is_active')
                        ->label('Status Aktif')
                        ->helperText('Jika dimatikan, pertanyaan ini akan disembunyikan dari aplikasi pengguna.')
                        ->default(true),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('question')
                    ->label('Pertanyaan')
                    ->searchable()
                    ->limit(50)
                    ->weight('bold'),
                    
                Tables\Columns\ToggleColumn::make('is_active')
                    ->label('Status Tayang'),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Terakhir Diubah')
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ubah'),
                Tables\Actions\DeleteAction::make()->label('Hapus'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFaqs::route('/'),
            'create' => Pages\CreateFaq::route('/create'),
            'edit' => Pages\EditFaq::route('/{record}/edit'),
        ];
    }
}