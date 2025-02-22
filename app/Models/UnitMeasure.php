<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitMeasure extends Model
{
  protected $fillable = [
      'nombre',
      'abreviatura',
      'estado',
      'created_by',
  ];

  public function SupplyMaterials()
  {
    return $this->hasMany(SupplyMaterial::class);
  }
}
