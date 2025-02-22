<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
class ReporteComprasExport implements FromCollection, WithHeadings, WithMapping
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
      $row->fecha_compra,
      $row->nro_documento,
      $row->nro_autorizacion,
      $row->supplier->razonSocial,
      $row->estado_pago,
      $row->subtotal,
      $row->iva,
      $row->total,
    ];
  }

  public function headings(): array
  {
      return [
          'Fecha',
          '#Factura',
          'Cod. Autorización',
          'Proveedor',
          'Estad de Pago',
          'Subtotal',
          'IVA',
          'Total',
      ];
  }
}
