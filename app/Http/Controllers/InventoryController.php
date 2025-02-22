<?php

namespace App\Http\Controllers;

use App\Exports\ReporteInventariosExport;
use App\Models\Category;
use App\Models\ProductService;
use App\Models\SupplyMaterial;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

class InventoryController extends Controller
{
    public function generateReport(Request $request)
    {
      $category = null;
      $tipo_categoria = $request->input('tipo_categoria');
      $category_id = $request->input('category_id');
      if($tipo_categoria == 'PRIMERO'){
        $query = SupplyMaterial::where('estado','1')
          ->select(['id', 'codigo', 'nombre', 'unit_measure_id', 'category_id', 'stock_actual', 'precio_compra']);
      } else {
        $query = ProductService::where('estado','1')
          ->select(['id', 'codigo', 'nombre', 'category_id', 'precio_venta']);
      }
      if(!empty($category_id)){
        $query->where('category_id',$category_id);
        $category = Category::find($category_id);
      }
      $data = $query->get();
      $pdf = Pdf::loadView('reportes.inventarios', compact('data','tipo_categoria', 'category'));
      return $pdf->download('reporte_inventario.pdf');
    }

  public function exportReport(Request $request)
  {
    $category = null;
    $tipo_categoria = $request->input('tipo_categoria');
    $category_id = $request->input('category_id');
    if($tipo_categoria == 'PRIMERO'){
      $query = SupplyMaterial::where('estado','1')
        ->select(['id', 'codigo', 'nombre', 'unit_measure_id', 'category_id', 'stock_actual', 'precio_compra']);
    } else {
      $query = ProductService::where('estado','1')
        ->select(['id', 'codigo', 'nombre', 'category_id', 'precio_venta']);
    }
    if(!empty($category_id)){
      $query->where('category_id',$category_id);
      $category = Category::find($category_id);
    }
    $data = $query->get();
    return Excel::download(new ReporteInventariosExport($data), 'reporte_inventario.xlsx');
  }
}
