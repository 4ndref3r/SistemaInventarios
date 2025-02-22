<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\ManufacturingMaterial;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ReporteEmpleadosExport;
use Maatwebsite\Excel\Facades\Excel;

class EmployeeController extends Controller
{
  public function generateReport(Request $request)
  {
    $data = $request->all();
    $fecha_inicio = $request->input('fecha_inicio');
    $fecha_fin = $request->input('fecha_fin');
    $tipo = $request->input('tipo');
    $employee_id = $request->input('employee_id');
    $employee = null;
    $query = ManufacturingMaterial::query()
        ->join('table_material_employee', 'manufacturing_materials.id', '=', 'table_material_employee.manufacturing_material_id')
        ->join('supply_materials', 'table_material_employee.supply_material_id', '=', 'supply_materials.id')
        ->join('unit_measures', 'supply_materials.unit_measure_id', '=', 'unit_measures.id')
        ->select(
            'manufacturing_materials.id',
            'manufacturing_materials.fecha',
            'manufacturing_materials.employee_id',
            'manufacturing_materials.tipo',
            'supply_materials.nombre as material_nombre',
            'table_material_employee.cantidad',
            'unit_measures.abreviatura'
        )
        ->where('manufacturing_materials.estado','1')
        ->with('Employee');
    if (!empty($fecha_inicio)) {
        $query->whereDate('fecha', '>=', $fecha_inicio)
              ->whereDate('fecha', '<=', $fecha_fin);
    } else {
        $query->whereDate('fecha', $fecha_fin ?? now()->format('Y-m-d'));
    }
    if (!empty($tipo)) {
        $query->where('tipo', $tipo);
    }

    if (!empty($employee_id)) {
        $query->where('employee_id', $employee_id);
        $employee=Employee::find($employee_id);
    }
    $data = $query->get();
    $pdf = Pdf::loadView('reportes.empleados', compact('data','employee', 'tipo','fecha_inicio', 'fecha_fin'));
    return $pdf->download('reporte_empleados.pdf');
  }

  public function exportReport(Request $request)
  {
    $data = $request->all();
    $fecha_inicio = $request->input('fecha_inicio');
    $fecha_fin = $request->input('fecha_fin');
    $tipo = $request->input('tipo');
    $employee_id = $request->input('employee_id');
    $employee = null;
    $query = ManufacturingMaterial::query()
        ->join('table_material_employee', 'manufacturing_materials.id', '=', 'table_material_employee.manufacturing_material_id')
        ->join('supply_materials', 'table_material_employee.supply_material_id', '=', 'supply_materials.id')
        ->join('unit_measures', 'supply_materials.unit_measure_id', '=', 'unit_measures.id')
        ->select(
            'manufacturing_materials.id',
            'manufacturing_materials.fecha',
            'manufacturing_materials.employee_id',
            'manufacturing_materials.tipo',
            'supply_materials.nombre as material_nombre',
            'table_material_employee.cantidad',
            'unit_measures.abreviatura'
        )
        ->where('manufacturing_materials.estado','1')
        ->with('Employee');
    if (!empty($fecha_inicio)) {
        $query->whereDate('fecha', '>=', $fecha_inicio)
              ->whereDate('fecha', '<=', $fecha_fin);
    } else {
        $query->whereDate('fecha', $fecha_fin ?? now()->format('Y-m-d'));
    }
    if (!empty($tipo)) {
        $query->where('tipo', $tipo);
    }

    if (!empty($employee_id)) {
        $query->where('employee_id', $employee_id);
        $employee=Employee::find($employee_id);
    }
    $data = $query->get();
    return Excel::download(new ReporteEmpleadosExport($data), 'reporte_empleados.xlsx');
  }
}
