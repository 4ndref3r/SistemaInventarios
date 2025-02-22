<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
  protected $fillable = [
    'customer_id',
    'fecha_emision',
    'fecha_validez',
    'moneda',
    'tipo_cambio',
    'subtotal',
    'descuento',
    'iva',
    'total',
    'observacion',
    'estado',
    'created_by',
  ];

  public function Customer()
  {
    return $this->belongsTo(Customer::class);
  }

  public function Sales()
  {
    return $this->hasMany(Sale::class);
  } 
  public function quotationProduct()
  {
    return $this->hasMany(QuotationProduct::class, 'quotation_id');
  }

  public function ProductServices()
  {
    return $this->belongsToMany(ProductService::class,'quotation_product')
                ->withPivot('cantidad','precio_unitario');
  } 
}
