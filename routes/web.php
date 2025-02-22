<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ManufacturingMaterialController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\SaleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/ventas/{sale}', [SaleController::class, 'generateVoucher'])->name('ventas.show');
Route::get('/admin/ventas/pdf', [SaleController::class, 'generateReport'])->name('ventas.reporte');
Route::get('/admin/ventas/export', [SaleController::class, 'exportReport'])->name('ventas.export');
Route::get('/cotizacion/{quotation}', [QuotationController::class, 'generateVoucher'])->name('cotizacion.show');
Route::get('/material/{material}', [OrderController::class, 'generateVoucher'])->name('material.show');
Route::get('/inventory/pdf', [InventoryController::class, 'generateReport'])->name('inventory.show');
Route::get('/inventory/export', [InventoryController::class, 'exportReport'])->name('inventory.report');
Route::get('/compras/pdf', [PurchaseController::class, 'generateReport'])->name('compras.show');
Route::get('/compras/export', [PurchaseController::class, 'exportReport'])->name('compras.report');
Route::get('/clientes/pdf', [CustomerController::class, 'generateReport'])->name('clientes.show');
Route::get('/clientes/export', [CustomerController::class, 'exportReport'])->name('clientes.report');
Route::get('/empleados/pdf', [EmployeeController::class, 'generateReport'])->name('empleados.show');
Route::get('/empleados/export', [EmployeeController::class, 'exportReport'])->name('empleados.report');
Route::post('/material/import', [ManufacturingMaterialController::class, 'import'])->name('manufacturing-materials.import');
