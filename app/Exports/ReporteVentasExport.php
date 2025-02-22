<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
class ReporteVentasExport implements FromCollection, WithHeadings, WithMapping
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
      $row->fecha_venta,
      $row->customer->razonSocial ?? 'Sin nombre',
      $row->estado_pago,
      $row->forma_pago,
      $row->total,
    ];
  }

  public function headings(): array
  {
      return [
          'Fecha',
          'Cliente',
          'Estado',
          'Forma de Pago',
          'Total Bs.',
      ];
  }
}
