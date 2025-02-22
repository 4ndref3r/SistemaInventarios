<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductMaterial extends Pivot
{
    protected $table = 'table_product_material';
    public $timestamps = false;
    protected $fillable = [
        'product_service_id',
        'supply_material_id',
        'cantidad',
        'precio_unitario',
    ];

    public function productService()
    {
        return $this->belongsTo(ProductService::class, 'product_service_id');
    }

    public function material()
    {
        return $this->belongsTo(SupplyMaterial::class, 'supply_material_id');
    }
}
