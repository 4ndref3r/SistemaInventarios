<x-filament-widgets::widget>
    <x-filament::section>
        @if ($this->getClientsWithPendingPayments()->isNotEmpty())
            <div class="space-y-4">
                @foreach ($this->getClientsWithPendingPayments() as $client)
                    <div class="p-4 shadow-md rounded-lg flex justify-between items-center">
                        <h3 class="text-lg font-semibold">{{ $client->razonSocial }}</h3>
                        <p>Saldo pendiente: <span style="color: red; font-weight: bold;">${{ number_format($client->total_pending, 2) }}</span></p>
                    </div>
                @endforeach
            </div>
        @else
            <div class="p-4 shadow-md rounded-lg">
                <p class="text-center text-gray-600">¡Genial! No hay clientes deudores.</p>
            </div>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>

