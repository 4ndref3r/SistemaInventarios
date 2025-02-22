<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
class ReporteEmpleadosExport implements FromCollection, WithHeadings, WithMapping
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
        $row->fecha,
        $row->employee->nombres.'  '.$row->employee->primer_apellido ?? 'Sin nombre',
        $row->tipo,
        $row->material_nombre,
        $row->cantidad,
        $row->abreviatura,
      ];
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'Empleado',
            'Tipo',
            'Material',
            'Cantidad',
            'Unidad',
        ];
    }
}
