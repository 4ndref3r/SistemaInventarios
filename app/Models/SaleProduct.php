<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class SaleProduct extends Pivot
{
  protected $table = 'table_sale_detail';
  public $timestamps = false;
  protected $fillable = [
      'sale_id',
      'product_service_id',
      'cantidad',
      'precio_unitario',
  ];
  public function sales()
  {
      return $this->belongsTo(Sale::class);
  }

  public function products()
  {
      return $this->belongsTo(ProductService::class, 'product_service_id');
  }
}
