<?php

namespace App\Filament\Resources\ManufacturingMaterialResource\Pages;

use App\Filament\Resources\ManufacturingMaterialResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Response;

class CreateManufacturingMaterial extends CreateRecord
{
    protected static string $resource = ManufacturingMaterialResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::user()->id;
        $data['estado'] = 1;

        return $data;
    }
    
    protected function getRedirectUrl(): string
    {
      return route('material.show',['material' => $this->record->id]) ?? $this->getResource()::getUrl('index');
    }
}
