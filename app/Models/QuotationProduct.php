<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class QuotationProduct extends Pivot
{
    protected $table = 'table_quotation_product';
    public $timestamps = false;
    public $incrementing = false;
    protected $fillable = [
        'quotation_id',
        'product_service_id',
        'cantidad',
        'precio_unitario',
    ];
    public function quotations()
    {
        return $this->belongsTo(Quotation::class);
    }

    public function products()
    {
        return $this->belongsTo(ProductService::class, 'product_service_id');
    }
}
