<?php

namespace App\Filament\Resources\UnitMeasureResource\Pages;

use App\Filament\Resources\UnitMeasureResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUnitMeasures extends ListRecords
{
    protected static string $resource = UnitMeasureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
              ->modalHeading('Crear Unidad de Medida')
              ->modalWidth('md')
              ->modalDescription('Ingrese los datos de la nueva unidad de medida')
              ->modalIcon('heroicon-o-plus')
              ->label('Nueva Unidad de Medida'),
        ];
    }
}
