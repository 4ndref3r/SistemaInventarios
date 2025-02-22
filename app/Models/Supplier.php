<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
  protected $fillable = [
    'razonSocial',
    'nombre',
    'celular',
    'email',
    'nro_cuenta',
    'direccion',
    'estado',
    'created_by',
  ];

  public function Purchase()
  {
    return $this->hasMany(Purchase::class);
  }
}
