<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
class ReporteInventariosExport implements FromCollection, WithHeadings, WithMapping
{
  protected $data;
  /**
  * @return \Illuminate\Support\Collection
  */

  public function __construct($data)
  {
      $this->data = $data;
  }
  public function collection()
  {
      return $this->data;
  }

  public function map($row): array
  {
    return [
      $row->codigo,
      $row->nombre ?? 'Sin nombre',
      $row->unitMeasure->abreviatura,
      $row->category->nombre ?? 'Sin categoria',
      $row->stock_actual,
      number_format($row->precio_compra, 2),
      number_format($row->precio_venta, 2),
      number_format(($row->precio_compra ?? 0) * ($row->stock_actual ?? 0), 2),
      number_format(($row->precio_venta ?? 0) * ($row->stock_actual ?? 0), 2),
    ];
  }

  public function headings(): array
  {
      return [
          'Codigo',
          'Descripción',
          'Unidad',
          'Categoría',
          'Cantidad',
          'Precio Compra',
          'Precio Venta',
          'Total Compra',
          'Total Venta',
      ];
  }
}
