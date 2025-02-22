<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQuotation extends EditRecord
{
    protected static string $resource = QuotationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function calculateTotals()
    {
        $subtotal = collect($this->data['quotationProduct'] ?? [])->sum(function ($item){
          return ($item['cantidad'] ?? 0) * ($item['precio_unitario'] ?? 0);
        });
        $setting = Setting::where('key', 'iva')->first();
        $porcentajeIva = $setting ? $setting->value : 13;
        $iva = ($subtotal * $porcentajeIva) / 100;
        $total = $subtotal+$iva;
        $this->data['subtotal']=round($subtotal, 2);
        $this->data['iva']=round($iva, 2);
        $this->data['total']=round($total, 2);
    }
}
