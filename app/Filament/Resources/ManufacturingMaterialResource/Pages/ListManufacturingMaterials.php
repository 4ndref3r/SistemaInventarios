<?php

namespace App\Filament\Resources\ManufacturingMaterialResource\Pages;

use App\Filament\Resources\ManufacturingMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManufacturingMaterials extends ListRecords
{
    protected static string $resource = ManufacturingMaterialResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
