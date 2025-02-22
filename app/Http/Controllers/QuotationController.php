<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class QuotationController extends Controller
{
  public function generateVoucher($quotation_id)
  {
    $quotation = Quotation::findOrFail($quotation_id);
    $pdf = Pdf::loadView('comprobantes.cotizacion', compact('quotation'));
    return $pdf->download('cotizacion' . $quotation_id . '.pdf');
  }
}
