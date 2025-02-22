<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Models\Customer;
use App\Models\Supplier;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ClientsSuppliersEmployees extends StatsOverviewWidget
{
  protected function getCards(): array
  {
      return [
          Stat::make('Clientes', Customer::count())
              ->icon('heroicon-m-user-group')
              ->description('Total de clientes registrados')
              ->color('success'),

          Stat::make('Proveedores', Supplier::count())
              ->icon('heroicon-m-truck')
              ->description('Total de proveedores activos')
              ->color('primary'),

          Stat::make('Empleados', Employee::count())
              ->icon('heroicon-m-briefcase')
              ->description('Total de empleados')
              ->color('warning'),
      ];
  }
}
