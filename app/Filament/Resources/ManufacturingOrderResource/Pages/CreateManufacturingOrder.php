<?php

namespace App\Filament\Resources\ManufacturingOrderResource\Pages;

use App\Filament\Resources\ManufacturingOrderResource;
use App\Models\ManufacturingOrder;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateManufacturingOrder extends CreateRecord
{
    protected static string $resource = ManufacturingOrderResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::user()->id;
        $data['estado'] = 1;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return ManufacturingOrderResource::getUrl('index');
    }
}
