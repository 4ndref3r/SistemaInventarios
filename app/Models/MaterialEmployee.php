<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MaterialEmployee extends Pivot
{
    protected $table = 'table_material_employee';
    protected $fillable = ['manufacturing_material_id', 'supply_material_id', 'cantidad'];
    public $timestamps = false;

    public function manufacturingMaterial()
    {
        return $this->belongsTo(ManufacturingMaterial::class, 'manufacturing_material_id');
    }

    public function supplyMaterial()
    {
        return $this->belongsTo(SupplyMaterial::class, 'supply_material_id');
    }

  protected static function booted()
  {
    static::created(function ($materialEmployee) {
      self::updateStock($materialEmployee, 'create');
    });

    static::updated(function ($materialEmployee) {
      self::updateStock($materialEmployee, 'update');
    });

    static::deleted(function ($materialEmployee) {
      self::updateStock($materialEmployee, 'delete');
    });
  }

  private static function updateStock($materialEmployee, $action)
  {
    $manufacturingMaterial = $materialEmployee->manufacturingMaterial;
    $supplyMaterial = $materialEmployee->supplyMaterial;
    $newQuantity = $materialEmployee->cantidad;
    if(!$manufacturingMaterial || !$supplyMaterial) return;
    $tipo= $manufacturingMaterial->tipo;
    if($tipo === 'PERDIDA') return;
    if($action === 'create'){
      if($tipo==='SALIDA'){
        $supplyMaterial->decrement('stock_actual', $newQuantity);
      } elseif ($tipo === 'ENTRADA') {
        $supplyMaterial->increment('stock_actual', $newQuantity);
      }
    } elseif ($action === 'update'){
      $materialEmployeeOld = $materialEmployee->getOriginal();
      $supplyMaterialOriginal = SupplyMaterial::find($materialEmployeeOld['supply_material_id']);
      $oldQuantity = $materialEmployeeOld['cantidad'];
      if($supplyMaterialOriginal){
        if($tipo === 'SALIDA'){
          $supplyMaterialOriginal->increment('stock_actual', $oldQuantity);
          $supplyMaterial->decrement('stock_actual', $newQuantity);
        } elseif ($tipo === 'ENTRADA') {
          $supplyMaterialOriginal->decrement('stock_actual', $oldQuantity);
          $supplyMaterial->increment('stock_actual', $newQuantity);
        }
      }
    } elseif ($action === 'delete'){
        if($tipo === 'SALIDA'){
          $supplyMaterial->increment('stock_actual', $newQuantity);
        } elseif ($tipo === 'ENTRADA') {
          $supplyMaterial->decrement('stock_actual', $newQuantity);
        }
    }
  }
}
