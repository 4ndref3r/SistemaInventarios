<?php

namespace App\Filament\Pages;

use App\Models\Purchase;
use App\Models\Supplier;
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

class Compras extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use interactsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.compras';
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $title = 'Reporte de Compras';
    protected static ?int $navigationSort = 20;
    public ?array $data = [];
    public $showTable = false;
    public function mount(): void
    {
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
                  ->getStateUsing(fn ($rowLoop) => $rowLoop->iteration),
              TextColumn::make('fecha_compra')
                  ->label('Fecha')
                  ->date()
                  ->sortable(),
              TextColumn::make('nro_documento')
                  ->label('# Factura')
                  ->sortable(),
              TextColumn::make('nro_autorizacion')
                  ->label('Cod. Autorizacion'),
              TextColumn::make('supplier.razonSocial')
                ->label('Proveedor'),
              TextColumn::make('estado_pago')
                ->label('Estado de Pago')
                ->badge()
                ->color( fn(string $state) => match ($state){
                  'PAGADO' => 'success',
                  'PARCIAL' => 'warning',
                  'PENDIENTE' => 'danger',
                }),
              TextColumn::make('subtotal')
                ->label('Subtotal')
                ->summarize(Sum::make()->label('')->prefix('Total: ')),
              TextColumn::make('iva')
                ->label('IVA')
                ->summarize(Sum::make()->label('')->prefix('Total: ')),
              TextColumn::make('total')
                ->label('Total Bs.')
                ->sortable()
                ->summarize(Sum::make()->label('')->prefix('Total: ')),
          ])
          ->defaultSort('fecha_compra','desc')
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
        $query = Purchase::query()->where('estado','1')->with('Supplier');
        if (!$this->showTable) {
            return $query->whereRaw('1 = 0');
        }
        if (!empty($this->data['fecha_inicio'])) {
            $query->whereDate('fecha_compra', '>=', $this->data['fecha_inicio'])
                  ->whereDate('fecha_compra', '<=', $this->data['fecha_fin']);
        } else {
            $query->whereDate('fecha_compra', $this->data['fecha_fin'] ?? now()->format('Y-m-d'));
        }
        if (!empty($this->data['estado_pago'])) {
            $query->where('estado_pago', $this->data['estado_pago']);
        }

        if (!empty($this->data['supplier_id'])) {
            $query->where('supplier_id', $this->data['supplier_id']);
        }

        return $query;
    }

    public function getFormSchema(): array
    {

      $minFecha = Purchase::min('fecha_compra') ?? now()->subYear();
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
              ->label('Estado de la venta')
              ->options([
                'PENDIENTE' => 'Pendiente',
                'PARCIAL' => 'Parcial',
                'PAGADO' => 'Pagado',
              ])
              ->nullable()
              ->live(),
            Select::make('supplier_id')
              ->label('Proveedor')
              ->options(Supplier::all()->where('estado','1')->pluck('razonSocial','id'))
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
            'supplier_id' => null,
        ]);
        $this->showTable = false;
        $this->dispatch('refresh-table');
    }

    protected function exportToExcel()
    {
        return redirect()->route('compras.report', $this->data);
    }

    protected function generatePDF()
    {
        return redirect()->route('compras.show', $this->data);
    }
}
