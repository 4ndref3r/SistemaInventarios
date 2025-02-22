<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
      'nit',
      'razonSocial',
      'gerente',
      'celular',
      'email',
      'direccion',
      'cod_orden',
      'cod_empleado',
      'cod_factura',
      'cod_cotizacion',
      'iva',
      'estado',
      'created_by',
    ];
}
