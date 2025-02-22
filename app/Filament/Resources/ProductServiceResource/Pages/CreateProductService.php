<?php

namespace App\Filament\Resources\ProductServiceResource\Pages;

use App\Filament\Resources\ProductServiceResource;
use App\Models\ProductService;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CreateProductService extends CreateRecord
{
    protected static string $resource = ProductServiceResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            return DB::transaction(function () use ($data):Model {
                // Crear el producto/servicio
                $product = static::getModel()::create([
                    'codigo' => $data['codigo'],
                    'nombre' => $data['nombre'],
                    'descripcion' => $data['descripcion'] ?? null,
                    'category_id' => $data['category_id'],
                    'costo_neto' => $data['costo_neto'],
                    'mano_obra' => $data['mano_obra'],
                    'margen_ganancia' => $data['margen_ganancia'],
                    'precio_venta' => $data['precio_venta'],
                    'tiene_iva' => $data['tiene_iva'],
                ]);

                // Procesar los materiales
                if (!empty($data['SupplyMaterials'])) {
                    foreach ($data['SupplyMaterials'] as $material) {
                        $product->SupplyMaterials()->attach($material['supply_material_id'], [
                            'cantidad' => $material['cantidad'],
                            'precio_unitario' => $material['precio_unitario']
                        ]);
                    }
                }

                return $product;
            });
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error al crear el producto')
                ->body('Ha ocurrido un error al procesar la solicitud: ' . $e->getMessage())
                ->danger()
                ->send();

            // Re-lanzar la excepción para que Filament la maneje
            throw $e;
        }
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['costo_neto'] = collect($data['SupplyMaterials'] ?? [])
            ->sum(fn($item) => ($item['cantidad'] ?? 0) * ($item['precio_unitario'] ?? 0));

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return ProductServiceResource::getUrl('index');
    }
}
