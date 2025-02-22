<?php

namespace App\Http\Controllers;

use App\Exports\ReporteVentasExport;
use App\Models\Sale;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
{
    public function generateVoucher($sale_id)
    {
      $sale = Sale::findOrFail($sale_id);
      $pdf = Pdf::loadView('comprobantes.venta', compact('sale'));
      return $pdf->download('recibo_venta_' . $sale_id . '.pdf');
    }

    public function generateReport(Request $request)
    {
      $data = $request->all();
      $fecha_inicio = $request->input('fecha_inicio');
      $fecha_fin = $request->input('fecha_fin');
      $forma_pago = $request->input('forma_pago');
      $estado_pago = $request->input('estado_pago');
      $query = Sale::where('estado', '1');
      if(!empty($fecha_inicio)){
            $query->whereDate('fecha_venta', '>=', $fecha_inicio)
                  ->whereDate('fecha_venta', '<=', $fecha_fin);
      } else {
            $query->whereDate('fecha_venta', $fecha_fin ?? now()->format('Y-m-d'));
      }
      if(!empty($forma_pago)){
        $query->where('forma_pago',$forma_pago);
      }
      if(!empty($estado_pago)){
        $query->where('estado_pago',$estado_pago);
      }
      $data = $query->get();
      $pdf = Pdf::loadView('reportes.ventas', compact('data','forma_pago', 'estado_pago','fecha_inicio', 'fecha_fin'));
      return $pdf->download('reporte_ventas.pdf');
    }

    public function exportReport(Request $request)
    {
      $data = $request->all();
      $fecha_inicio = $request->input('fecha_inicio');
      $fecha_fin = $request->input('fecha_fin');
      $forma_pago = $request->input('forma_pago');
      $estado_pago = $request->input('estado_pago');
      $query = Sale::where('estado', '1');
      if(!empty($fecha_inicio)){
            $query->whereDate('fecha_venta', '>=', $fecha_inicio)
                  ->whereDate('fecha_venta', '<=', $fecha_fin);
      } else {
            $query->whereDate('fecha_venta', $fecha_fin ?? now()->format('Y-m-d'));
      }
      if(!empty($forma_pago)){
        $query->where('forma_pago',$forma_pago);
      }
      if(!empty($estado_pago)){
        $query->where('estado_pago',$estado_pago);
      }
      $data = $query->get();
      return Excel::download(new ReporteVentasExport($data), 'reporte_ventas.xlsx');
    }
}
