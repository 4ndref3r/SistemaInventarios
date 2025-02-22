<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingOrder extends Model
{
  protected $fillable = [
    'codigo',
    'fecha_inicio',
    'fecha_fin',
    'proceso',
    'product_service_id',
    'cantidad',
    'observacion',
    'estado',
    'created_by',
  ];

  public function ManufacturingMaterials()
  {
    return $this->hasMany(ManufacturingMaterial::class);
  }

  public function Employees()
  {
    return $this->belongsToMany(Employee::class,'employee_order')->withPivot('fecha_asignacion');
  }
  public function ProductService()
  {
    return $this->belongsTo(ProductService::class);
  }
  public function employeeOrders()
  {
    return $this->hasMany(EmployeeOrder::class, 'manufacturing_order_id');
  }
}
