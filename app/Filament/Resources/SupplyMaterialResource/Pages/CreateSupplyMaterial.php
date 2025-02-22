<?php

namespace App\Filament\Resources\SupplyMaterialResource\Pages;

use App\Filament\Resources\SupplyMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSupplyMaterial extends CreateRecord
{
    protected static string $resource = SupplyMaterialResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::user()->id;
        $data['estado'] = 1;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return SupplyMaterialResource::getUrl('index');
    }
}
