<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
  protected $fillable = [
    'supplier_id',
    'nro_documento',
    'nro_autorizacion',
    'fecha_compra',
    'fecha_vencimiento',
    'moneda',
    'tipo_cambio',
    'subtotal',
    'iva',
    'total',
    'pagado',
    'pago_pendiente',
    'tipo_compra',
    'estado_pago',
    'forma_pago',
    'observacion',
    'estado',
    'created_by',
  ];

  public function Supplier()
  {
    return $this->belongsTo(Supplier::class);
  }

  public function purchaseDetails()
  {
    return $this->hasMany(PurchaseDetail::class);
  }

  protected static function booted()
  {
    static::updated(function ($purchase) {
      if($purchase->isDirty('estado')){
        static::actualizarStockRelacionados($purchase);
      }
    });

    static::deleting(function ($purchase) {
      $purchase->purchaseDetails()->each(function ($purchaseDetail) {
        $purchaseDetail->delete();
      });
    });
  }

  protected static function actualizarStockRelacionados($purchase)
  {
    $details = $purchase->purchaseDetails;
    foreach ($details as $detail) {
      $supplyMaterial = $detail->supplyMaterial;
      if($supplyMaterial){
        $cantidad = $detail->cantidad;
        if($purchase->estado == 0){
          $supplyMaterial->decrement('stock_actual', $cantidad);
        } elseif ($purchase->estado == 1) {
          $supplyMaterial->increment('stock_actual', $cantidad);
        }
      }
    }
  }
}
