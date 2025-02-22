<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\MaterialesImport;
use Illuminate\Support\Facades\Session;

class ManufacturingMaterialController extends Controller
{
    public function import(Request $request)
    {
        $request->validate([
            'excelFile' => 'required|mimes:xlsx,xls'
        ]);
        try {
            Excel::import(new MaterialesImport, $request->file('excelFile'));
            return back()->with('success', '¡Importación completada con éxito!');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al importar: ' . $e->getMessage());
        }
    }
}
