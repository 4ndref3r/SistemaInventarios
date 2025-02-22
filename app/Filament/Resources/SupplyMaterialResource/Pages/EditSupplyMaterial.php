<?php

namespace App\Filament\Resources\SupplyMaterialResource\Pages;

use App\Filament\Resources\SupplyMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSupplyMaterial extends EditRecord
{
    protected static string $resource = SupplyMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
