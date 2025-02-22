<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
  protected $fillable = [
      'codigo',
      'nombre',
      'descripcion',
      'tipo_categoria',
      'estado',
      'created_by',
  ];

  public function SupplyMaterials()
  {
    return $this->hasMany(SupplyMaterial::class);
  }

  public function ProductServices()
  {
    return $this->hasMany(ProductService::class);
  }
}
