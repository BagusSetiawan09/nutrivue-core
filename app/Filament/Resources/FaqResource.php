<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FaqResource\Pages;
use App\Models\Faq;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;

class FaqResource extends Resource
{
    protected static ?string $model = Faq::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';
    
    protected static ?string $navigationLabel = 'Manajemen FAQ';
    
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
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()->label('Lihat Detail')->color('gray'),
                    Tables\Actions\EditAction::make()->label('Ubah'),
                    Tables\Actions\DeleteAction::make()->label('Hapus'),
                ])
                ->label('Aksi')
                ->icon('heroicon-m-ellipsis-vertical')
                ->button()
                ->color('gray'),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistSection::make('Informasi Panduan Bantuan')
                    ->description('Detail lengkap dari panduan yang akan ditampilkan di aplikasi.')
                    ->schema([
                        TextEntry::make('question')
                            ->label('Pertanyaan')
                            ->weight('bold')
                            ->size(TextEntry\TextEntrySize::Large)
                            ->columnSpanFull(),
                            
                        TextEntry::make('answer')
                            ->label('Jawaban Lengkap')
                            ->prose() // Memberikan format tipografi yang nyaman dibaca untuk teks panjang
                            ->columnSpanFull(),
                            
                        IconEntry::make('is_active')
                            ->label('Status Penayangan')
                            ->boolean(),
                            
                        TextEntry::make('updated_at')
                            ->label('Terakhir Diperbarui')
                            ->dateTime()
                            ->badge()
                            ->color('info'),
                    ])->columns(2)
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