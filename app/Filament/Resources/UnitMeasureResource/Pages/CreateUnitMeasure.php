<?php

namespace App\Filament\Resources\UnitMeasureResource\Pages;

use App\Filament\Resources\UnitMeasureResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUnitMeasure extends CreateRecord
{
    protected static string $resource = UnitMeasureResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::user()->id;
        $data['estado'] = 1;

        return $data;
    }
}
