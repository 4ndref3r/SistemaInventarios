<?php

namespace App\Filament\Resources\SaleResource\Widgets;

use App\Models\Sale;
use Filament\Widgets\ChartWidget;
use Flowframe\Trend\Trend;
use Flowframe\Trend\TrendValue;

class LatestSale extends ChartWidget
{
    protected static ?string $heading = 'Estadisticas de Ventas';
    public ?string $filter = 'year';

    protected function getData(): array
    {
        $activeFilter = $this->filter;
        
        // Definir rangos de fechas según el filtro seleccionado
        switch ($activeFilter) {
            case 'today':
                $startDate = now()->startOfDay();
                $endDate = now()->endOfDay();
                $data = Trend::model(Sale::class)
                    ->between(
                        start: $startDate,
                        end: $endDate,
                    )
                    ->perHour()
                    ->count();
                break;
            case 'week':
                $startDate = now()->subWeek()->startOfDay();
                $endDate = now()->endOfDay();
                $data = Trend::model(Sale::class)
                    ->between(
                        start: $startDate,
                        end: $endDate,
                    )
                    ->perDay()
                    ->count();
                break;
            case 'month':
                $startDate = now()->subMonth()->startOfDay();
                $endDate = now()->endOfDay();
                $data = Trend::model(Sale::class)
                    ->between(
                        start: $startDate,
                        end: $endDate,
                    )
                    ->perDay()
                    ->count();
                break;
            case 'year':
            default:
                $startDate = now()->startOfYear();
                $endDate = now()->endOfYear();
                $data = Trend::model(Sale::class)
                    ->between(
                        start: $startDate,
                        end: $endDate,
                    )
                    ->perMonth()
                    ->count();
                break;
        }
        
        // Extraer y formatear datos para evitar el error de htmlspecialchars
        $salesValues = [];
        $labels = [];
        
        foreach ($data as $item) {
            $salesValues[] = (int) $item->aggregate;
            
            // Formatear las etiquetas según el tipo de filtro
            if ($activeFilter === 'today') {
                // Para filtros por hora
                $labels[] = date('H:i', strtotime($item->date));
            } elseif ($activeFilter === 'week' || $activeFilter === 'month') {
                // Para filtros diarios
                $labels[] = date('d M', strtotime($item->date));
            } else {
                // Para filtros mensuales
                $labels[] = date('M Y', strtotime($item->date));
            }
        }
        
        return [
            'datasets' => [
                [
                    'label' => 'Ventas',
                    'data' => $salesValues,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => 'white',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hoy',
            'week' => 'Anterior Semana',
            'month' => 'Anterior Mes',
            'year' => 'Este año',
        ];
    }
}
