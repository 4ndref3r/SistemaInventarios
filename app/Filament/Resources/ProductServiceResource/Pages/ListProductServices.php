<?php

namespace App\Filament\Resources\ProductServiceResource\Pages;

use App\Filament\Resources\ProductServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProductServices extends ListRecords
{
    protected static string $resource = ProductServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
