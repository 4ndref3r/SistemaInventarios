<?php

namespace App\Filament\Resources\QuotationResource\Pages;

use App\Filament\Resources\QuotationResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CreateQuotation extends CreateRecord
{
    protected static string $resource = QuotationResource::class;
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::user()->id;
        $data['estado'] = 1;

        return $data;
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

    protected function getRedirectUrl(): string
    {
      return route('cotizacion.show',['quotation' => $this->record->id]) ?? $this->getResource()::getUrl('index');
    }
}
