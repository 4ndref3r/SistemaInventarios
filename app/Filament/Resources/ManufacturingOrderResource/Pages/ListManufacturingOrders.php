<?php

namespace App\Filament\Resources\ManufacturingOrderResource\Pages;

use App\Filament\Resources\ManufacturingOrderResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManufacturingOrders extends ListRecords
{
    protected static string $resource = ManufacturingOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
