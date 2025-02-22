<?php

namespace App\Http\Controllers;

use App\Exports\ReporteComprasExport;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class PurchaseController extends Controller
{
  public function generateReport(Request $request)
  {
    $fecha_inicio = $request->input('fecha_inicio');
    $fecha_fin = $request->input('fecha_fin');
    $estado_pago = $request->input('estado_pago');
    $forma_pago = $request->input('supplier_id');
    $supplier = null;
    $query = Purchase::where('estado', '1');
    if(!empty($fecha_inicio)){
          $query->whereDate('fecha_compra', '>=', $fecha_inicio)
                ->whereDate('fecha_compra', '<=', $fecha_fin);
    } else {
          $query->whereDate('fecha_compra', $fecha_fin ?? now()->format('Y-m-d'));
    }
    if(!empty($estado_pago)){
      $query->where('estado_pago',$estado_pago);
    }
    if(!empty($supplier_id)){
      $query->where('forma_pago',$forma_pago);
      $supplier = Supplier::find($supplier_id);
    }
    $data = $query->get();
    $pdf = Pdf::loadView('reportes.compras', compact('data','supplier', 'estado_pago','fecha_inicio', 'fecha_fin'));
    return $pdf->download('reporte_compras.pdf');
  }

  public function exportReport(Request $request)
  {
    $fecha_inicio = $request->input('fecha_inicio');
    $fecha_fin = $request->input('fecha_fin');
    $estado_pago = $request->input('estado_pago');
    $forma_pago = $request->input('supplier_id');
    $supplier = null;
    $query = Purchase::where('estado', '1');
    if(!empty($fecha_inicio)){
          $query->whereDate('fecha_compra', '>=', $fecha_inicio)
                ->whereDate('fecha_compra', '<=', $fecha_fin);
    } else {
          $query->whereDate('fecha_compra', $fecha_fin ?? now()->format('Y-m-d'));
    }
    if(!empty($estado_pago)){
      $query->where('estado_pago',$estado_pago);
    }
    if(!empty($supplier_id)){
      $query->where('forma_pago',$forma_pago);
      $supplier = Supplier::find($supplier_id);
    }
    $data = $query->get();
    return Excel::download(new ReporteComprasExport($data), 'reporte_compras.xlsx');
  }
}
