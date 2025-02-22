<?php

namespace App\Http\Controllers;

use App\Models\ManufacturingMaterial;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class OrderController extends Controller
{
  public function generateVoucher($manufacturing_material_id)
  {
    $material = ManufacturingMaterial::findOrFail($manufacturing_material_id);
    $pdf = Pdf::loadView('comprobantes.material', compact('material'));
    return $pdf->download('orden_material' . $manufacturing_material_id . '.pdf');
  }
}
