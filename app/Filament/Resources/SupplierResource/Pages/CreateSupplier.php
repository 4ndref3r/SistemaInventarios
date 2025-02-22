<?php

namespace App\Filament\Resources\SupplierResource\Pages;

use App\Filament\Resources\SupplierResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateSupplier extends CreateRecord
{
    protected static string $resource = SupplierResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::user()->id;
        $data['estado'] = 1;

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return SupplierResource::getUrl('index');
    }
}
