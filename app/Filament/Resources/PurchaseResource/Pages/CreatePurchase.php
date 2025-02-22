<?php

namespace App\Filament\Resources\PurchaseResource\Pages;

use App\Filament\Resources\PurchaseResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePurchase extends CreateRecord
{
    protected static string $resource = PurchaseResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::user()->id;
        $data['estado'] = 1;

        return $data;
    }

    public function calculateTotals()
    {
      $subtotal = collect($this->data['purchaseDetails'] ?? [])->sum(function ($item) {
        return $item['subtotal'] ?? 0;
      });
      $setting = Setting::where('key', 'iva')->first();
      $porcentajeIva = $setting ? $setting->value : 13;
      $iva = ($subtotal * $porcentajeIva) / 100;
      $total = $subtotal + $iva;
      $this->data['subtotal']=round($subtotal, 2);
      $this->data['iva']=round($iva, 2); 
      $this->data['total']=round($total, 2); 
      $this->data['pagado']=round($total, 2); 
       
    }

    protected function getRedirectUrl(): string
    {
        return PurchaseResource::getUrl('index');
    }
}
