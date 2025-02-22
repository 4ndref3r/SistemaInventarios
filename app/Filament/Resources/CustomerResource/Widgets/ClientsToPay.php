<?php

namespace App\Filament\Resources\CustomerResource\Widgets;

use App\Models\Customer;
use Filament\Widgets\Widget;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ClientsToPay extends Widget
{
    protected static string $view = 'filament.resources.customer-resource.widgets.clients-to-pay';

    public function getClientsWithPendingPayments()
    {
        $clients = Customer::withSum('Sales as total_pending', 'saldo_pendiente')
            ->whereHas('Sales', function ($query) {
                $query->where('estado', '1')
                      ->where(function ($query) {
                          $query->where('estado_pago', 'PENDIENTE')
                                ->orWhere('estado_pago', 'PARCIAL');
                      });
            })
            ->orderByDesc('total_pending')
            ->limit(10)
            ->get();
        return $clients;
    }
}
