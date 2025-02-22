<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Sale;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\ReporteClientesExport;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
  public function generateReport(Request $request)
  {
    $fecha_inicio = $request->input('fecha_inicio');
    $fecha_fin = $request->input('fecha_fin');
    $estado_pago = $request->input('estado_pago');
    $customer_id = $request->input('customer_id');
    $customer = null;
    $query = Sale::query()
        ->join('customers', 'sales.customer_id', '=', 'customers.id')
        ->join('table_sale_detail', 'table_sale_detail.sale_id', '=', 'sales.id')
        ->join('product_services', 'table_sale_detail.product_service_id', '=', 'product_services.id')
        ->select(
            'customers.razonSocial',
            'sales.fecha_venta',
            'sales.id',
            'sales.estado_pago',
            'sales.total',
            'product_services.nombre as producto_nombre',
            'table_sale_detail.cantidad',
            'table_sale_detail.precio_unitario'
        )
        ->where('sales.estado','1');
    if (!empty($fecha_inicio)) {
        $query->whereDate('fecha_venta', '>=', $fecha_inicio)
              ->whereDate('fecha_venta', '<=', $fecha_fin);
    } else {
        $query->whereDate('fecha_venta', $fecha_fin ?? now()->format('Y-m-d'));
    }
    if (!empty($estado_pago)) {
        $query->where('estado_pago', $estado_pago);
    }

    if (!empty($customer_id)) {
        $query->where('customer_id', $customer_id);
        $customer = Customer::find($customer_id);
    }
    $data = $query->get();
    $pdf = Pdf::loadView('reportes.clientes', compact('data','fecha_inicio', 'fecha_fin', 'estado_pago', 'customer'));
    return $pdf->download('reporte_clientes.pdf');
  }

  public function exportReport(Request $request)
  {
    $data = $request->all();
    $fecha_inicio = $request->input('fecha_inicio');
    $fecha_fin = $request->input('fecha_fin');
    $estado_pago = $request->input('estado_pago');
    $customer_id = $request->input('customer_id');
    $customer = null;
    $query = Sale::query()
        ->join('customers', 'sales.customer_id', '=', 'customers.id')
        ->join('table_sale_detail', 'table_sale_detail.sale_id', '=', 'sales.id')
        ->join('product_services', 'table_sale_detail.product_service_id', '=', 'product_services.id')
        ->select(
            'customers.razonSocial',
            'sales.fecha_venta',
            'sales.id',
            'sales.estado_pago',
            'sales.total',
            'product_services.nombre as producto_nombre',
            'table_sale_detail.cantidad',
            'table_sale_detail.precio_unitario'
        )
        ->where('sales.estado','1');
    if (!empty($fecha_inicio)) {
        $query->whereDate('fecha_venta', '>=', $fecha_inicio)
              ->whereDate('fecha_venta', '<=', $fecha_fin);
    } else {
        $query->whereDate('fecha_venta', $fecha_fin ?? now()->format('Y-m-d'));
    }
    if (!empty($estado_pago)) {
        $query->where('estado_pago', $estado_pago);
    }

    if (!empty($customer_id)) {
        $query->where('customer_id', $customer_id);
        $customer = Customer::find($customer_id);
    }
    $data = $query->get();
    return Excel::download(new ReporteClientesExport($data), 'reporte_clientes.xlsx');
  }
}
