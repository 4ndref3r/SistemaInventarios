<?php

namespace App\Filament\Pages;

use App\Models\Employee;
use App\Models\ManufacturingMaterial;
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

class Empleados extends Page implements HasForms, HasTable
{
    use InteractsWithForms;
    use interactsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.empleados';
    protected static bool $shouldRegisterNavigation = true;
    protected static ?string $navigationGroup = 'Reportes';
    protected static ?string $title = 'Reporte de Empleados';
    protected static ?int $navigationSort = 21;
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
                  ->label('#')
                  ->getStateUsing(fn ($rowLoop) => $rowLoop->iteration),
              TextColumn::make('fecha')
                  ->label('Fecha')
                  ->date('d-m-Y')
                  ->sortable(),
              TextColumn::make('employee.nombres')
                  ->label('Empleado')
                  ->formatStateUsing(fn ($record) => "{$record->Employee->nombres} {$record->Employee->primer_apellido}")
                  ->sortable(),
              TextColumn::make('tipo')
                  ->label('Tipo')
                  ->sortable(),
              TextColumn::make('material_nombre')
                  ->label('Materiales')
                  ->sortable(),
              TextColumn::make('cantidad')
                  ->label('Cantidad')
                  ->sortable(),
              TextColumn::make('abreviatura')
                  ->label('Unidad de Medida'),
          ])
          ->defaultSort('fecha','desc')
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
        $query = ManufacturingMaterial::query()
            ->join('table_material_employee', 'manufacturing_materials.id', '=', 'table_material_employee.manufacturing_material_id')
            ->join('supply_materials', 'table_material_employee.supply_material_id', '=', 'supply_materials.id')
            ->join('unit_measures', 'supply_materials.unit_measure_id', '=', 'unit_measures.id')
            ->select(
                'manufacturing_materials.id',
                'manufacturing_materials.fecha',
                'manufacturing_materials.employee_id',
                'manufacturing_materials.tipo',
                'supply_materials.nombre as material_nombre',
                'table_material_employee.cantidad',
                'unit_measures.abreviatura'
            )
            ->where('manufacturing_materials.estado','1')
            ->with('Employee');
        if (!$this->showTable) {
            return $query->whereRaw('1 = 0');
        }
        if (!empty($this->data['fecha_inicio'])) {
            $query->whereDate('fecha', '>=', $this->data['fecha_inicio'])
                  ->whereDate('fecha', '<=', $this->data['fecha_fin']);
        } else {
            $query->whereDate('fecha', $this->data['fecha_fin'] ?? now()->format('Y-m-d'));
        }
        if (!empty($this->data['tipo'])) {
            $query->where('tipo', $this->data['tipo']);
        }

        if (!empty($this->data['employee_id'])) {
            $query->where('employee_id', $this->data['employee_id']);
        }

        return $query;
    }

    public function getFormSchema(): array
    {

      $minFecha = ManufacturingMaterial::min('fecha') ?? now()->subYear();
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
            Select::make('tipo')
              ->label('Tipo')
              ->options([
                'ENTRADA' => 'Entrada',
                'SALIDA' => 'Salida',
                'PERDIDA' => 'Pérdida',
              ])
              ->nullable()
              ->live(),
            Select::make('employee_id')
              ->label('Empleado')
              ->options(Employee::query()->selectRaw("nombres || ' ' || primer_apellido as name, id")->pluck('name', 'id'))
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
            'forma_pago' => null,
        ]);
        $this->showTable = false;
        $this->dispatch('refresh-table');
    }

    protected function exportToExcel()
    {
        return redirect()->route('empleados.report', $this->data);
    }

    protected function generatePDF()
    {
        return redirect()->route('empleados.show', $this->data);
    }
}
