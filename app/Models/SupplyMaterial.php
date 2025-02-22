<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupplyMaterial extends Model
{
  protected $fillable = [
      'codigo',
      'nombre',
      'descripcion',
      'category_id',
      'unit_measure_id',
      'stock_actual',
      'stock_minimo',
      'stock_comprometido',
      'precio_compra',
      'precio_venta',
      'ubicacion',
      'estado',
      'created_by',
  ];

  public function UnitMeasure()
  {
    return $this->belongsTo(UnitMeasure::class);
  }

  public function Category()
  {
    return $this->belongsTo(Category::class);
  }

  public function products()
  {
    return $this->hasMany(ProductMaterial::class);
  }
  public function materialEmployees()
  {
    return $this->hasMany(MaterialEmployee::class);
  }

  public function purchaseDetails()
  {
    return $this->hasMany(PurchaseDetail::class);
  }
}
