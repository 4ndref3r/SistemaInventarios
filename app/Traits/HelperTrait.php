<?php
namespace App\Traits;
use App\Models\Setting;

trait HelperTrait
{
  public static function generarCodigoPorModelo($modelo)
    {
        $mapaCampos = [
            'ManufacturingOrder' => 'cod_orden',
            'Employee' => 'cod_empleado',
            'Quotation' => 'cod_cotizacion',
            'Sale' => 'cod_factura',
        ];
        if (!array_key_exists($modelo, $mapaCampos)) {
            return ''; 
        }
        $campo = $mapaCampos[$modelo];
        $settings = \App\Models\Setting::first();
        if (!$settings || !isset($settings->$campo) || empty($settings->$campo)) {
            return '';
        }
        $prefijo = $settings->$campo;
        $ultimoCodigo = app("App\Models\\" . $modelo)::orderBy('codigo', 'desc')->first();
        if ($ultimoCodigo === null) {
            $secuencia = str_pad(1, 6, '0', STR_PAD_LEFT);
        } else {
            $ultimoNumero = (int) substr($ultimoCodigo->codigo, -6);
            $secuencia = str_pad($ultimoNumero + 1, 6, '0', STR_PAD_LEFT);
        }
        return $prefijo . ' - ' . $secuencia;
    }
}