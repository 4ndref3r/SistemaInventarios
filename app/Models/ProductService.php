<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductService extends Model
{
  protected $fillable = [
    'codigo',
    'nombre',
    'descripcion',
    'category_id',
    'costo_neto',
    'mano_obra',
    'precio_venta',
    'margen_ganancia',
    'tiene_iva',
    'estado',
    'created_by',
  ];
  public function quotationProduct()
  {
    return $this->hasMany(QuotationProduct::class, 'product_service_id');
  }

  public function saleProduct()
  {
    return $this->hasMany(SaleProduct::class, 'product_service_id');
  }

  public function materials()
  {
    return $this->hasMany(ProductMaterial::class);
  }

  public function ManufacturingOrder()
  {
    return $this->hasMany(ManufacturingOrder::class);
  }

  public function Category()
  {
    return $this->belongsTo(Category::class);
  }
}
