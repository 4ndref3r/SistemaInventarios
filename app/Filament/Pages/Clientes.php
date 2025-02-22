<?php

namespace App\Filament\Pages;

use App\Models\Customer;
use App\Models\Sale;
use Filament\Pages\Page;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Actions\Action;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Carbon\Carbon;
use Filament\Support\Enums\ActionSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Columns\Summarizers\Sum;

class Clientes extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use interactsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.clientes';
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $title = 'Reporte de Clientes';
    protected static ?int $navigationSort = 23;
    public ?array $data = [];
    public $showTable = false;
    public function mount(): void
    {
        $this->data = [];
        $this->form->fill([
            'fecha_fin' => now()->format('Y-m-d'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('data')
            ->live();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(fn () => $this->getTableQuery())
            ->columns([
              TextColumn::make('index')
                  ->label('#')
                  ->getStateUsing(fn ($rowLoop) => $rowLoop->iteration),
              TextColumn::make('fecha_venta')
                  ->label('Fecha')
                  ->date('d-m-Y')
                  ->sortable(),
              TextColumn::make('razonSocial')
                  ->label('Cliente')
                  ->sortable(),
              TextColumn::make('estado_pago')
                  ->badge()
                  ->color(fn (string $state)=>match ($state){
                    'PAGADO' => 'success',
                    'PARCIAL' => 'warning',
                    'PENDIENTE' => 'danger',
                  })
                  ->label('Tipo')
                  ->sortable(),
              TextColumn::make('producto_nombre')
                  ->label('Producto')
                  ->sortable(),
              TextColumn::make('cantidad')
                  ->label('Cantidad')
                  ->sortable(),
              TextColumn::make('precio_unitario')
                  ->label('P.Unitario')
                  ->numeric(decimalPlaces: 2)
                  ->suffix(' Bs.-'),
              TextColumn::make('total')
                  ->label('Total Bs.')
                  ->numeric(decimalPlaces: 2)
                  ->suffix(' Bs.-')
                  ->summarize(Sum::make()->label('')->prefix('Total: ')),
          ])
          ->defaultSort('fecha_venta','desc')
          ->headerActions([
              TableAction::make('export')
                    ->label('Excel')
                    ->icon('heroicon-o-document-arrow-down')
                    ->button()
                    ->color('success')
                    ->action(fn () => $this->exportToExcel()),
              TableAction::make('pdf')
                    ->label('PDF')
                    ->icon('heroicon-o-document')
                    ->button()
                    ->color('danger')
                    ->action(fn () => $this->generatePDF()),
          ]);
    }

    protected function getTableQuery():Builder
    {
        $query = Sale::query()
            ->join('customers', 'sales.customer_id', '=', 'customers.id')
            ->join('table_sale_detail', 'table_sale_detail.sale_id', '=', 'sales.id')
            ->join('product_services', 'table_sale_detail.product_service_id', '=', 'product_services.id')
            ->select(
                'customers.razonSocial',
                'sales.fecha_venta',
                'sales.id',
                'sales.estado_pago',
                'sales.total',
                'product_services.nombre as producto_nombre',
                'table_sale_detail.cantidad',
                'table_sale_detail.precio_unitario'
            )
            ->where('sales.estado','1');
        if (!$this->showTable) {
            return $query->whereRaw('1 = 0');
        }
        if (!empty($this->data['fecha_inicio'])) {
            $query->whereDate('fecha_venta', '>=', $this->data['fecha_inicio'])
                  ->whereDate('fecha_venta', '<=', $this->data['fecha_fin']);
        } else {
            $query->whereDate('fecha_venta', $this->data['fecha_fin'] ?? now()->format('Y-m-d'));
        }
        if (!empty($this->data['estado_pago'])) {
            $query->where('estado_pago', $this->data['estado_pago']);
        }

        if (!empty($this->data['customer_id'])) {
            $query->where('customer_id', $this->data['customer_id']);
        }

        return $query;
    }

    public function getFormSchema(): array
    {

      $minFecha = Sale::min('fecha_venta') ?? now()->subYear();
      $hoy = today();
      return [
        Grid::make(4)
          ->schema([
            DatePicker::make('fecha_inicio')
              ->label('Fecha de Inicio')
              ->minDate($minFecha)
              ->maxDate(fn ($get) => $get('fecha_fin') ?? $hoy)
              ->live(),
            DatePicker::make('fecha_fin')
              ->label('Fecha de Fin')
              ->default(fn() => now())
              ->maxDate(now())
              ->minDate(fn ($get) => $get('fecha_inicio') ?? $minFecha)
              ->required()
              ->live(),
            Select::make('estado_pago')
              ->label('Estado de compra')
              ->options([
                'PENDIENTE' => 'Pendiente',
                'PARCIAL' => 'Parcial',
                'PAGADO' => 'Pagado',
              ])
              ->nullable()
              ->live(),
            Select::make('customer_id')
              ->label('Cliente')
              ->options(Customer::all()->pluck('razonSocial', 'id'))
              ->nullable()
              ->live(),
          ])
      ];
    }

    public function generate()
    {
      $this->showTable=true;
      $this->dispatch('refresh-table');
    }

    public function resetForm()
    {
        $this->form->fill([
            'fecha_fin' => now()->format('Y-m-d'),
            'fecha_inicio' => null,
            'estado_pago' => null,
            'customer_id' => null,
        ]);
        $this->showTable = false;
        $this->dispatch('refresh-table');
    }

    protected function exportToExcel()
    {
        return redirect()->route('clientes.report', $this->data);
    }

    protected function generatePDF()
    {
        return redirect()->route('clientes.show', $this->data);
    }
}
