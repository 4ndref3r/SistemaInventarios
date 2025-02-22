<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PurchaseDetail extends Pivot
{
    protected $table = 'table_purchase_detail';
    public $timestamps = false;
    protected $fillable = [
        'purchase_id',
        'product_material_id',
        'cantidad',
        'precio_unitario',
        'subtotal',
    ];

    protected static function booted()
    {
        static::created(function ($purchaseDetail) {
            $purchase = $purchaseDetail->supplyMaterial;
            $purchase->stock_actual += $purchaseDetail->cantidad;
            $purchase->save();
        });

        static::deleted(function ($purchaseDetail) {
            $purchase = $purchaseDetail->supplyMaterial;
            $purchase->stock_actual -= $purchaseDetail->cantidad;
            $purchase->save();
        });

        static::updated(function ($purchaseDetail) {
            $purchase = $purchaseDetail->supplyMaterial;
            $cantidadAnterior = $purchaseDetail->getOriginal('cantidad');
            $diferencia = $purchaseDetail->cantidad - $cantidadAnterior;
            $purchase->stock_actual += $diferencia;
            $purchase->save();
        });
    }
    public function purchase()
    {
        return $this->belongsTo(Purchase::class, 'purchase_id');
    }

    public function supplyMaterial()
    {
        return $this->belongsTo(SupplyMaterial::class, 'product_material_id');
    }
}
