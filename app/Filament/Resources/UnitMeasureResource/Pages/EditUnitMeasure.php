<?php

namespace App\Filament\Resources\UnitMeasureResource\Pages;

use App\Filament\Resources\UnitMeasureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUnitMeasure extends EditRecord
{
    protected static string $resource = UnitMeasureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
