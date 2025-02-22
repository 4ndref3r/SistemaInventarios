<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
class ReporteClientesExport implements FromCollection, WithHeadings, WithMapping
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
      $row->fecha_vebta,
      $row->razonSocial ?? 'Sin nombre',
      $row->estado_pago,
      $row->producto_nombre,
      $row->cantidad,
      $row->precio_unitario,
      $row->total,
    ];
  }

  public function headings(): array
  {
      return [
          'Fecha',
          'Cliente',
          'Tipo',
          'Producto',
          'Cantidad',
          'Precio',
          'Totol Bs',
      ];
  }
}
