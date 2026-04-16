<?php

namespace App\Filament\Resources\TitikPenyaluranResource\Pages;

use App\Filament\Resources\TitikPenyaluranResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTitikPenyalurans extends ListRecords
{
    protected static string $resource = TitikPenyaluranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
