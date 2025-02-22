<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeOrder extends Pivot
{
    protected $table = 'table_employee_order';
    public $timestamps = false;
    protected $fillable = [
      'manufacturing_order_id', 
      'employee_id', 
      'fecha_asignacion',
      ];
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function manufacturingOrder()
    {
        return $this->belongsTo(ManufacturingOrder::class);
    }
}
