<?php

namespace App\Filament\Resources\TitikPenyaluranResource\Pages;

use App\Filament\Resources\TitikPenyaluranResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTitikPenyaluran extends EditRecord
{
    protected static string $resource = TitikPenyaluranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
