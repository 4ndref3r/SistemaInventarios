<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\SupplyMaterial;
use App\Models\UnitMeasure;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class MaterialImport implements ToCollection, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function collection(Collection $rows)
    {
        Log::info('Comenzando la importación de materiales.');
        foreach($rows as $index => $row)
        {
          $row = $row->map(function ($value) {
              return is_string($value) ? strtoupper(trim($value)) : $value;
          });
          if ($row->filter()->isEmpty()) {
              Log::info("Fila #$index vacía, saltada.");
              continue;
          }
          Log::info("Procesando fila #$index", $row->toArray());
          $validator = Validator::make($row->toArray(), [
            'codigo' => 'required|unique:supply_materials,codigo',
            'nombre' => 'required|string',
            'cod_categoria' => 'required|string',
            'unidad_nombre' => 'required|string',
            'stock_actual' => 'required|numeric|min:0',
            'precio_compra' => 'required|numeric|min:0',
            'precio_venta' => 'nullable|numeric|min:0',
          ]);

          if ($validator->fails()) {
              Log::warning("Fila #$index no válida", $validator->errors()->toArray());
              continue;
          }
          Log::info("Fila #$index validada correctamente.");
          $category = Category::firstOrCreate(
              ['codigo' => $row['cod_categoria']],
              [
                  'codigo' => $row['cod_categoria'],
                  'nombre' => $row['nombre_categoria'], 
                  'tipo_categoria' => 'PRIMERO', 
                  'estado' => 1, 
                  'created_by' => Auth::id(),
              ]
          );
          Log::info("Categoría procesada: " . $category->nombre);
          $unitOfMeasure = UnitMeasure::firstOrCreate(
              ['nombre' => $row['unidad_nombre']],
              [
                  'nombre' => $row['unidad_nombre'],
                  'abreviatura' => $row['unidad_abreviatura'],
                  'estado' => 1,
                  'created_by' => Auth::id(),
              ]
          );
          Log::info("Unidad de medida procesada: " . $unitOfMeasure->nombre);

          $existingMaterial = SupplyMaterial::where('codigo', $row['codigo'])->first();
          if ($existingMaterial) {
              Log::info("Material con código {$row['codigo']} ya existe. Saltando...");
              continue;
          }

          SupplyMaterial::create([
            'codigo' => $row['codigo'],
            'nombre' => $row['nombre'],
            'descripcion' => $row['descripcion'] ?? null,
            'category_id' => $category->id,
            'unit_measure_id' => $unitOfMeasure->id,
            'ubicacion' => $row['ubicacion'] ?? null,
            'stock_actual' => $row['stock_actual'],
            'precio_compra' => $row['precio_compra'],
            'precio_venta' => $row['precio_venta'] ?? null,
          ]);
          Log::info("Material de suministro creado: " . $row['codigo']);
        }
        Log::info('Importación de materiales finalizada.');
    }
}
