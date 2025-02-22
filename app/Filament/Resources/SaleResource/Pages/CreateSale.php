<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Filament\Resources\SaleResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::user()->id;
        $data['estado'] = 1;

        return $data;
    }
    protected function getRedirectUrl(): string
    {
      return route('ventas.show',['sale' => $this->record->id]) ?? $this->getResource()::getUrl('index');
    }
}
