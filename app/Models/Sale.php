<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
  protected $fillable = [
    'customer_id',
    'nro_documento',
    'nro_autorizacion',
    'fecha_venta',
    'moneda',
    'tipo_cambio',
    'subtotal',
    'descuento',
    'iva',
    'total',
    'pagado',
    'saldo_pendiente',
    'tipo_compra',
    'estado_pago',
    'forma_pago',
    'quotation_id',
    'observacion',
    'estado',
    'created_by',
  ];

  public function Customer()
  {
    return $this->belongsTo(Customer::class);
  }

  public function Quotation()
  {
    return $this->belongsTo(Quotation::class);
  }
  public function saleProduct()
  {
    return $this->hasMany(SaleProduct::class,'sale_id');
  }
}
