<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingMaterial extends Model
{
  protected $fillable = [
    'manufacturing_order_id',
    'employee_id',
    'cantidad',
    'tipo',
    'user_id',
    'supply_material_id',
    'fecha',
    'entregado_por',
    'observacion',
    'estado',
    'created_by',
  ];

  public function Employee()
  {
    return $this->belongsTo(Employee::class);
  }

  public function ManufacturingOrder()
  {
    return $this->belongsTo(ManufacturingOrder::class);
  }

  public function supplyMaterials()
  {
    return $this->hasMany(MaterialEmployee::class, 'manufacturing_material_id');
  }

  protected static function booted()
  {

    static::created(function ($model) {
        \Log::info('Modelo creado');
    });

    static::updated(function ($manufacturingMaterial) {
      if($manufacturingMaterial->isDirty('estado') || $manufacturingMaterial->isDirty('tipo')){
        static::actualizarStockRelacionados($manufacturingMaterial);
      }
    });

    static::deleting(function ($manufacturingMaterial) {
      $manufacturingMaterial->supplyMaterials()->each(function ($supplyMaterial) {
        $supplyMaterial->delete();
      });
    });
  }

  protected static function actualizarStockRelacionados($manufacturingMaterial)
  {
    $materialEmployees = $manufacturingMaterial->supplyMaterials;
    foreach ($materialEmployees as $materialEmployee) {
      $supplyMaterial = $materialEmployee->supplyMaterial;
      if($supplyMaterial){
        $cantidad = $materialEmployee->cantidad;
        if($manufacturingMaterial->estado == 0){
          if($manufacturingMaterial->tipo == 'SALIDA'){
            $supplyMaterial->increment('stock_actual', $cantidad);
          }elseif ($manufacturingMaterial->tipo == 'ENTRADA') {
            $supplyMaterial->decrement('stock_actual', $cantidad);
          }
        } elseif ($manufacturingMaterial->estado == 1) {
          if($manufacturingMaterial->tipo == 'SALIDA'){
            $supplyMaterial->decrement('stock_actual', $cantidad);
          }elseif ($manufacturingMaterial->tipo == 'ENTRADA') {
            $supplyMaterial->increment('stock_actual', $cantidad);
          }
        }
      }
    }
  }
}
