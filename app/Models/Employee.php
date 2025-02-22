<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
  protected $fillable = [
    'codigo',
    'nombres',
    'primer_apellido',
    'segundo_apellido',
    'ci_nit',
    'celular',
    'email',
    'direccion',
    'fecha_nacimiento',
    'fecha_ingreso',
    'fecha_cese',
    'estado',
    'created_by',
  ];

  public function ManufacturingMaterials()
  {
    return $this->hasMany(ManufacturingMaterial::class);
  }

  public function ManufacturingOrders()
  {
    return $this->hasMany(ManufacturingOrder::class);
  }

  public function EmployeeOrders()
  {
    return $this->hasMany(EmployeeOrder::class, 'employee_id');
  }
}
